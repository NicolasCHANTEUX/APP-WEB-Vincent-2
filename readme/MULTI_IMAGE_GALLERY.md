# 🖼️ Système de Galerie Multi-Images

## Vue d'ensemble

Le système de galerie multi-images permet d'associer **jusqu'à 6 images** par produit, avec :
- **Upload multiple** : drag & drop ou sélection fichiers
- **Réorganisation** : drag & drop pour changer l'ordre
- **Image principale** : sélection manuelle de l'image mise en avant
- **3 formats par image** : original, format1 (détail), format2 (miniature)
- **Gestion automatique** : conversion WebP, redimensionnement, suppression

---

## 📋 Architecture

### 1. Base de données

**Table : `product_images`**

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | INT unsigned | Clé primaire auto-increment |
| `product_id` | INT unsigned | FK vers `product.id` (CASCADE) |
| `filename` | VARCHAR(255) | Nom du fichier (ex: `PAI-PAG1-format1-1.webp`) |
| `position` | INT | Ordre d'affichage (1, 2, 3, 4, 5, 6) |
| `is_primary` | TINYINT(1) | Booléen : 1 = image principale, 0 = secondaire |
| `created_at` | DATETIME | Date d'upload |

**Contraintes :**
- **Foreign Key** : `product_id` → `product.id` (CASCADE delete/update)
- **Index** : `product_id`, `position`, `is_primary`

**Migration :** `app/Database/Migrations/2026-01-08-000000_CreateProductImagesTable.php`

### 2. Modèle

**Fichier :** `app/Models/ProductImageModel.php`

**Méthodes principales :**

```php
// Récupérer toutes les images d'un produit (ordonnées par position)
getProductImages($productId): array

// Récupérer l'image principale (ou la première si aucune)
getPrimaryImage($productId): ?array

// Définir une image comme principale (transaction)
setPrimaryImage($imageId): bool

// Mettre à jour les positions (drag & drop)
updatePositions(array $positions): bool

// Compter les images d'un produit
countProductImages($productId): int

// Supprimer toutes les images d'un produit
deleteProductImages($productId): bool

// Obtenir la prochaine position disponible
getNextPosition($productId): int
```

### 3. Service de traitement d'images

**Fichier :** `app/Libraries/ImageProcessor.php`

**Nouveautés :**

```php
// Traiter une image avec numérotation (1-6)
processProductImage(File $file, string $sku, int $imageNumber = 1): array

// Supprimer un set complet (3 formats) pour un numéro donné
deleteProductImageSet(string $sku, int $imageNumber): bool

// Supprimer TOUTES les images d'un produit (6 × 3 formats)
deleteAllProductImages(string $sku): int

// Vérifier l'existence d'un set d'images
imageSetExists(string $sku, int $imageNumber): bool

// Obtenir l'URL publique (compatible ancien/nouveau format)
getImageUrl(string $filename, string $format = 'format1'): string
```

**Convention de nommage :**

```
Ancien format : PAI-PAG1-format1.webp
Nouveau format : PAI-PAG1-format1-1.webp
                              └─ Numéro d'image (1-6)
```

**Formats générés par image :**
- `PAI-PAG1-1.webp` (original : 1920px, qualité 90)
- `PAI-PAG1-format1-1.webp` (détail : 800px, qualité 85)
- `PAI-PAG1-format2-1.webp` (miniature : 350px, qualité 70)

### 4. Contrôleur Admin

**Fichier :** `app/Controllers/AdminProduitsController.php`

**API RESTful ajoutée :**

| Méthode | URL | Description |
|---------|-----|-------------|
| **GET** | `/admin/produits/{id}/images` | Récupérer toutes les images |
| **POST** | `/admin/produits/{id}/images/upload` | Upload multi-fichiers |
| **PUT** | `/admin/produits/images/{imageId}/set-primary` | Définir image principale |
| **PUT** | `/admin/produits/{id}/images/reorder` | Réorganiser (drag & drop) |
| **DELETE** | `/admin/produits/images/{imageId}` | Supprimer une image |

**Exemple de requête upload :**

```javascript
const formData = new FormData();
formData.append('images[]', file1);
formData.append('images[]', file2);

fetch('/admin/produits/42/images/upload', {
    method: 'POST',
    body: formData
});
```

**Réponse upload :**

```json
{
  "success": true,
  "uploaded": [
    {
      "id": 123,
      "filename": "PAI-PAG1-format1-1.webp",
      "url": "http://kayart.test/uploads/format1/PAI-PAG1-format1-1.webp",
      "position": 1,
      "is_primary": true
    },
    {
      "id": 124,
      "filename": "PAI-PAG1-format1-2.webp",
      "url": "http://kayart.test/uploads/format1/PAI-PAG1-format1-2.webp",
      "position": 2,
      "is_primary": false
    }
  ],
  "errors": [],
  "total_images": 2
}
```

### 5. Interface Admin

**Fichier :** `app/Views/components/section/admin/edit_produit_section.php`

**Fonctionnalités :**

1. **Zone d'upload drag & drop**
   - Drag & drop de fichiers
   - Clic pour sélectionner
   - Loader pendant l'upload
   - Validation côté client (6 max, 10 MB par image)

2. **Grille des images**
   - Affichage en grid 2-3 colonnes
   - Badge "Principale" sur l'image sélectionnée
   - Bouton étoile pour définir comme principale
   - Bouton poubelle pour supprimer
   - Handle de drag pour réorganiser
   - Numéro de position affiché

3. **Interactions drag & drop**
   - Glisser une image pour la déplacer
   - Mise à jour automatique en BDD
   - Restauration en cas d'erreur

**JavaScript inclus :**
- `loadExistingImages()` : Charge les images au chargement de la page
- `handleFiles()` : Upload multi-fichiers avec FormData
- `renderImages()` : Affiche la grille avec tri par position
- `setPrimaryImage()` : Appel API PUT pour définir principale
- `deleteImage()` : Appel API DELETE avec confirmation
- `initializeDragAndDrop()` : Gestion du drag & drop
- `updatePositions()` : Sauvegarde l'ordre après drag

---

## 🔧 Installation & Migration

### 1. Exécuter les migrations

```bash
php spark migrate
```

Cela créera :
- La table `product_images`
- Les index nécessaires
- La clé étrangère CASCADE

### 2. Vérifier la structure

```sql
DESCRIBE product_images;

+------------+------------------+------+-----+---------+
| Field      | Type             | Null | Key | Default |
+------------+------------------+------+-----+---------+
| id         | int unsigned     | NO   | PRI | NULL    |
| product_id | int unsigned     | NO   | MUL | NULL    |
| filename   | varchar(255)     | NO   |     | NULL    |
| position   | int              | NO   | MUL | NULL    |
| is_primary | tinyint(1)       | NO   | MUL | 0       |
| created_at | datetime         | YES  |     | NULL    |
+------------+------------------+------+-----+---------+

SHOW CREATE TABLE product_images;
-- Vérifier la présence de la contrainte FOREIGN KEY CASCADE
```

### 3. Tester l'interface

1. Accéder à `/admin/produits/edit/1` (remplacer 1 par un ID existant)
2. Vérifier que la section "Galerie d'images" s'affiche
3. Tester l'upload d'une image
4. Vérifier la génération des 3 formats dans `public/uploads/`

---

## 📖 Guide d'utilisation (Admin)

### Ajouter des images

1. **Méthode 1 : Drag & Drop**
   - Glisser 1 à 6 images dans la zone grisée
   - Les images sont automatiquement uploadées et traitées

2. **Méthode 2 : Sélection manuelle**
   - Cliquer sur la zone d'upload
   - Sélectionner jusqu'à 6 fichiers (Ctrl+clic)
   - Cliquer sur "Ouvrir"

3. **Résultat**
   - Chaque image génère 3 fichiers (original, détail, miniature)
   - La première image uploadée devient automatiquement l'image principale
   - Les images s'affichent dans la grille

### Définir l'image principale

- Cliquer sur l'**icône étoile** (en haut à droite) de l'image souhaitée
- L'image reçoit un badge jaune "⭐ Principale"
- L'ancienne image principale perd son badge
- Cette image sera affichée en premier dans la galerie visiteur

### Réorganiser les images

- **Glisser** l'image par le handle (icône grip en bas à gauche)
- **Déposer** à la nouvelle position
- L'ordre est sauvegardé automatiquement en base de données
- Les numéros de position se mettent à jour

### Supprimer une image

- Cliquer sur le **bouton poubelle** (rouge, en bas à droite)
- Confirmer la suppression
- Les 3 formats sont supprimés (fichiers + BDD)
- Si c'était l'image principale, la première image restante devient principale

### Compteur d'images

- Affiché en haut à droite : **(X/6 images)**
- Maximum : 6 images par produit
- Si 6 images existent déjà, l'upload est bloqué

---

## 🎨 Affichage Visiteur (Frontend)

### À implémenter (prochaine étape)

**Fichier cible :** `app/Views/components/section/produits/product_detail_content.php`

**Design proposé : Galerie horizontale**

```
┌─────────────────────────────────────┐
│                                     │
│        GRANDE IMAGE (800×600)       │
│           format1                   │
│                                     │
└─────────────────────────────────────┘

┌──────┬──────┬──────┬──────┬──────┐
│ IMG1 │ IMG2 │ IMG3 │ IMG4 │ IMG5 │  ← Miniatures cliquables
└──────┴──────┴──────┴──────┴──────┘
        format2 (350×350)
```

**Comportement :**
- Au chargement : afficher l'image principale en grand
- Au clic sur miniature : changer l'image affichée
- Au hover sur miniature : border accent-gold
- Mobile : stack vertical (grande image + miniatures en dessous)

**Exemple de code à ajouter :**

```php
<?php 
$productImageModel = new \App\Models\ProductImageModel();
$images = $productImageModel->getProductImages($product['id']);
$primaryImage = $productImageModel->getPrimaryImage($product['id']);
?>

<!-- Grande image -->
<div id="main-image-container" class="mb-4">
    <img id="main-image" 
         src="<?= $imageProcessor->getImageUrl($primaryImage['filename'], 'format1') ?>" 
         alt="<?= esc($product['title']) ?>"
         class="w-full h-auto rounded-xl shadow-lg">
</div>

<!-- Miniatures -->
<div class="flex gap-2 overflow-x-auto">
    <?php foreach ($images as $image): ?>
    <img src="<?= $imageProcessor->getImageUrl($image['filename'], 'format2') ?>" 
         alt="Image <?= $image['position'] ?>"
         class="w-20 h-20 object-cover rounded-lg cursor-pointer border-2 border-gray-200 hover:border-accent-gold transition"
         onclick="changeMainImage('<?= $imageProcessor->getImageUrl($image['filename'], 'format1') ?>')">
    <?php endforeach; ?>
</div>

<script>
function changeMainImage(url) {
    document.getElementById('main-image').src = url;
}
</script>
```

### Intégration avec les cards produits

**Fichier :** `app/Views/components/section/produits/products_grid.php`

**Modification à apporter :**

```php
<?php
$productImageModel = new \App\Models\ProductImageModel();
foreach ($products as $product):
    $primaryImage = $productImageModel->getPrimaryImage($product['id']);
    $imageUrl = $primaryImage 
        ? $imageProcessor->getImageUrl($primaryImage['filename'], 'format2')
        : asset('images/placeholder.webp');
?>
    <img src="<?= $imageUrl ?>" alt="<?= esc($product['title']) ?>">
<?php endforeach; ?>
```

---

## 🔐 Sécurité

### Validations

1. **Upload**
   - Types autorisés : JPEG, PNG, WebP
   - Taille max : 10 MB par fichier
   - Limite : 6 images par produit

2. **Authentification**
   - Routes protégées par filtre `adminauth`
   - Session admin obligatoire

3. **CSRF**
   - Token CSRF sur tous les formulaires
   - Vérifié automatiquement par CodeIgniter

4. **Noms de fichiers**
   - SKU nettoyé (regex `[^a-zA-Z0-9-_]` supprimé)
   - Pas de caractères spéciaux
   - Écrasement impossible (numérotation unique)

### Permissions fichiers

```bash
chmod 755 public/uploads/
chmod 755 public/uploads/original/
chmod 755 public/uploads/format1/
chmod 755 public/uploads/format2/
chmod 644 public/uploads/*/*.webp
```

---

## 🐛 Debugging

### Logs

Tous les événements sont loggés dans `writable/logs/log-YYYY-MM-DD.log` :

```
[AdminProduits] === UPLOAD MULTI-IMAGES PRODUIT #42 ===
[ImageProcessor] === DÉBUT TRAITEMENT IMAGE ===
[ImageProcessor] SKU: PAI-PAG1
[ImageProcessor] Image #1
[ImageProcessor] ✓ original généré : PAI-PAG1-1.webp (245678 bytes)
[ImageProcessor] ✓ format1 généré : PAI-PAG1-format1-1.webp (123456 bytes)
[ImageProcessor] ✓ format2 généré : PAI-PAG1-format2-1.webp (45678 bytes)
[AdminProduits] ✓ Image #1 uploadée: PAI-PAG1-format1-1.webp
```

### Vérifier les images générées

```bash
ls -lh public/uploads/original/
ls -lh public/uploads/format1/
ls -lh public/uploads/format2/

# Exemple de sortie :
# PAI-PAG1-1.webp         245 KB  (original)
# PAI-PAG1-format1-1.webp 123 KB  (détail)
# PAI-PAG1-format2-1.webp  45 KB  (miniature)
```

### Console navigateur

```javascript
// Inspecter les images chargées
console.table(images);

// Tester l'upload
const formData = new FormData();
formData.append('images[]', file);
fetch('/admin/produits/42/images/upload', {
    method: 'POST',
    body: formData
}).then(r => r.json()).then(console.log);
```

### Erreurs courantes

| Erreur | Cause | Solution |
|--------|-------|----------|
| `Limite de 6 images atteinte` | Tentative d'upload >6 | Supprimer une image d'abord |
| `Fichier trop volumineux` | Image >10 MB | Compresser l'image avant upload |
| `Type de fichier non autorisé` | Format non supporté | Utiliser JPEG, PNG ou WebP |
| `Produit introuvable` | ID invalide | Vérifier l'ID du produit |
| `Erreur sauvegarde BDD` | Contrainte FK violée | Vérifier que le produit existe |
| Images non affichées | Permissions incorrectes | `chmod 755 uploads/` |

---

## 🚀 Roadmap

### ✅ Fait

- [x] Migration table `product_images`
- [x] Modèle `ProductImageModel` (10 méthodes)
- [x] Service `ImageProcessor` avec numérotation
- [x] API RESTful (5 endpoints)
- [x] Interface admin drag & drop
- [x] Upload multiple
- [x] Réorganisation drag & drop
- [x] Sélection image principale
- [x] Suppression individuelle

### 🔄 En cours

- [ ] Galerie frontend visiteur (horizontal)
- [ ] Lightbox/zoom au clic
- [ ] Mise à jour des cards produits (image principale)

### 📅 À venir

- [ ] Migration données anciennes images (si besoin)
- [ ] Lazy loading images
- [ ] Progressive image loading (LQIP)
- [ ] Watermark automatique (optionnel)
- [ ] Compression intelligente (WebP quality adaptive)
- [ ] Support SVG pour logos/icônes
- [ ] Génération automatique alt text (IA)

---

## 📊 Performance

### Poids moyen par produit

- **6 images × 3 formats = 18 fichiers**
- Poids moyen : ~1.5 MB par produit
- 100 produits = ~150 MB de stockage

### Optimisations futures

1. **CDN** : Déporter les images sur un CDN (Cloudflare, AWS)
2. **Lazy loading** : Charger les images au scroll
3. **WebP progressive** : Afficher LQIP puis HD
4. **Cache navigateur** : Headers `Cache-Control: max-age=31536000`

---

## 🧪 Tests

### Test manuel complet

1. ✅ Créer un nouveau produit
2. ✅ Éditer le produit
3. ✅ Uploader 1 image → Vérifier 3 fichiers générés
4. ✅ Uploader 5 images supplémentaires → Total 6
5. ✅ Tenter d'uploader une 7ème → Blocage
6. ✅ Définir image #3 comme principale → Badge jaune
7. ✅ Drag image #1 vers position #5 → Ordre changé
8. ✅ Supprimer image #2 → 5 images restantes
9. ✅ Supprimer le produit → Toutes les images supprimées (CASCADE)

### Tests unitaires (à créer)

```php
// tests/unit/ProductImageModelTest.php
public function testGetProductImages()
public function testSetPrimaryImage()
public function testUpdatePositions()
public function testCascadeDelete()
```

---

## 📞 Support

**Documentation :**
- [CodeIgniter 4 Images](https://codeigniter.com/user_guide/libraries/images.html)
- [WebP Guide](https://developers.google.com/speed/webp)

**Fichiers clés :**
- Migration : `app/Database/Migrations/2026-01-08-000000_CreateProductImagesTable.php`
- Modèle : `app/Models/ProductImageModel.php`
- Service : `app/Libraries/ImageProcessor.php`
- Contrôleur : `app/Controllers/AdminProduitsController.php`
- Vue : `app/Views/components/section/admin/edit_produit_section.php`
- Routes : `app/Config/Routes.php`

---

**Version :** 1.0.0  
**Date :** 2026-01-08  
**Auteur :** KayArt Development Team
