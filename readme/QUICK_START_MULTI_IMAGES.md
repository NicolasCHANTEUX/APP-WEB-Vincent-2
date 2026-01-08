# 🔄 Système Multi-Images - Guide d'Exécution

## ✅ Étapes Complétées

### 1. Base de données ✅
- [x] Migration `CreateProductImagesTable.php` créée
- [x] Table avec 6 colonnes + indexes + FK CASCADE

### 2. Modèle ✅
- [x] `ProductImageModel.php` créé (10 méthodes)
- [x] Gestion complète CRUD + positions + primary

### 3. Service Images ✅
- [x] `ImageProcessor.php` modifié
- [x] Support numérotation (SKU-format1-1.webp)
- [x] Méthodes suppression set/bulk

### 4. Contrôleur Admin ✅
- [x] 5 méthodes API ajoutées à `AdminProduitsController.php`
- [x] GET images, POST upload, PUT primary, PUT reorder, DELETE

### 5. Routes ✅
- [x] 5 routes API ajoutées dans `Routes.php`
- [x] Toutes protégées par filtre `adminauth`

### 6. Interface Admin ✅
- [x] `edit_produit_section.php` modifiée
- [x] Drag & drop upload + réorganisation
- [x] JavaScript complet (350+ lignes)

### 7. Documentation ✅
- [x] `MULTI_IMAGE_GALLERY.md` créé (500+ lignes)
- [x] Guide complet architecture + usage

---

## 🔨 Prochaines Étapes (À Faire Maintenant)

### Étape 1 : Exécuter la migration

```bash
php spark migrate
```

**Vérification :**
```sql
USE kayart_db;
DESCRIBE product_images;
SHOW CREATE TABLE product_images;
```

### Étape 2 : Tester l'interface admin

1. Se connecter à l'admin : `/connexion?lang=fr`
2. Aller sur `/admin/produits?lang=fr`
3. Éditer un produit existant
4. Vérifier que la section "Galerie d'images" s'affiche
5. Tester l'upload d'une image

**Si erreur 404 :** Vérifier que les routes sont bien chargées

### Étape 3 : Créer la galerie visiteur

**Fichier à modifier :** `app/Views/components/section/produits/product_detail_content.php`

**Rechercher la section image actuelle et remplacer par :**

```php
<?php
use App\Models\ProductImageModel;
use App\Libraries\ImageProcessor;

$productImageModel = new ProductImageModel();
$imageProcessor = new ImageProcessor();
$images = $productImageModel->getProductImages($product['id']);
$primaryImage = $productImageModel->getPrimaryImage($product['id']);

// Si aucune image dans la nouvelle table, essayer l'ancien champ "image"
if (empty($images) && !empty($product['image'])) {
    $primaryImageUrl = $imageProcessor->getImageUrl($product['image'], 'format1');
} else {
    $primaryImageUrl = $primaryImage 
        ? $imageProcessor->getImageUrl($primaryImage['filename'], 'format1')
        : asset('images/placeholder.webp');
}
?>

<!-- Galerie d'images -->
<div class="mb-6">
    <!-- Grande image -->
    <div id="main-image-container" class="mb-4 bg-gray-50 rounded-2xl overflow-hidden">
        <img id="main-image" 
             src="<?= $primaryImageUrl ?>" 
             alt="<?= esc($product['title']) ?>"
             width="800"
             height="600"
             class="w-full h-auto object-contain">
    </div>

    <?php if (!empty($images) && count($images) > 1): ?>
    <!-- Miniatures (si plusieurs images) -->
    <div class="flex gap-3 overflow-x-auto pb-2">
        <?php foreach ($images as $image): ?>
        <img src="<?= $imageProcessor->getImageUrl($image['filename'], 'format2') ?>" 
             alt="Image <?= $image['position'] ?>"
             width="80"
             height="80"
             class="w-20 h-20 flex-shrink-0 object-cover rounded-lg cursor-pointer border-2 <?= $image['is_primary'] ? 'border-accent-gold' : 'border-gray-200 hover:border-accent-gold' ?> transition"
             onclick="changeMainImage('<?= $imageProcessor->getImageUrl($image['filename'], 'format1') ?>')">
        <?php endforeach; ?>
    </div>

    <script>
    function changeMainImage(url) {
        const mainImage = document.getElementById('main-image');
        mainImage.style.opacity = '0.5';
        mainImage.src = url;
        mainImage.onload = () => mainImage.style.opacity = '1';
    }
    </script>
    <?php endif; ?>
</div>
```

### Étape 4 : Mettre à jour les cards produits

**Fichier :** `app/Views/components/section/produits/products_grid.php`

**Trouver l'affichage de l'image et modifier :**

```php
<?php
use App\Models\ProductImageModel;
use App\Libraries\ImageProcessor;

$productImageModel = new ProductImageModel();
$imageProcessor = new ImageProcessor();
?>

<?php foreach ($products as $product): 
    // Priorité : image principale → ancien champ image → placeholder
    $primaryImage = $productImageModel->getPrimaryImage($product['id']);
    
    if ($primaryImage) {
        $imageUrl = $imageProcessor->getImageUrl($primaryImage['filename'], 'format2');
    } elseif (!empty($product['image'])) {
        $imageUrl = $imageProcessor->getImageUrl($product['image'], 'format2');
    } else {
        $imageUrl = asset('images/placeholder.webp');
    }
?>

<img src="<?= $imageUrl ?>" 
     alt="<?= esc($product['title']) ?>"
     width="350"
     height="350"
     class="w-full h-64 object-cover">

<?php endforeach; ?>
```

### Étape 5 : Compiler Tailwind (si nécessaire)

```bash
npm run build
```

### Étape 6 : Tests complets

#### Test 1 : Upload basique
1. Éditer un produit
2. Uploader 1 image
3. Vérifier dans `public/uploads/` :
   - `original/SKU-1.webp`
   - `format1/SKU-format1-1.webp`
   - `format2/SKU-format2-1.webp`
4. Vérifier en BDD :
   ```sql
   SELECT * FROM product_images WHERE product_id = 1;
   ```

#### Test 2 : Upload multiple
1. Uploader 5 images supplémentaires (total 6)
2. Vérifier 18 fichiers créés (6 × 3)
3. Essayer d'en uploader une 7ème → Erreur attendue

#### Test 3 : Image principale
1. Cliquer sur l'étoile de l'image #3
2. Vérifier badge "Principale" affiché
3. Vérifier en BDD :
   ```sql
   SELECT id, filename, is_primary FROM product_images WHERE product_id = 1;
   -- is_primary = 1 uniquement pour image #3
   ```

#### Test 4 : Réorganisation
1. Glisser l'image #1 vers la position #5
2. Vérifier l'ordre change visuellement
3. Recharger la page → Ordre conservé

#### Test 5 : Suppression
1. Supprimer l'image #2
2. Vérifier 5 images restantes
3. Vérifier fichiers supprimés dans `public/uploads/`
4. Si c'était l'image principale → Vérifier qu'une autre a pris sa place

#### Test 6 : Affichage visiteur
1. Aller sur `/produits/slug-du-produit?lang=fr`
2. Vérifier que l'image principale s'affiche en grand
3. Cliquer sur une miniature → Image principale change
4. Vérifier sur mobile → Responsive

#### Test 7 : CASCADE delete
1. Supprimer le produit entier
2. Vérifier en BDD :
   ```sql
   SELECT * FROM product_images WHERE product_id = 1;
   -- Résultat vide
   ```
3. Vérifier fichiers supprimés dans `public/uploads/`

---

## 🐛 Résolution de Problèmes

### Erreur "Table product_images doesn't exist"

**Cause :** Migration non exécutée

**Solution :**
```bash
php spark migrate:status
php spark migrate
```

### Erreur 404 sur les routes API

**Cause :** Routes non chargées ou filtre admin manquant

**Solution :**
```bash
# Vérifier les routes
php spark routes | grep "images"

# Résultat attendu :
# GET    | admin/produits/(:num)/images              | AdminProduitsController::getImages/$1
# POST   | admin/produits/(:num)/images/upload       | AdminProduitsController::uploadImages/$1
# PUT    | admin/produits/images/(:num)/set-primary  | AdminProduitsController::setPrimaryImage/$1
# PUT    | admin/produits/(:num)/images/reorder      | AdminProduitsController::reorderImages/$1
# DELETE | admin/produits/images/(:num)              | AdminProduitsController::deleteImage/$1
```

### Images non affichées (403 Forbidden)

**Cause :** Permissions incorrectes

**Solution :**
```bash
chmod 755 public/uploads/
chmod 755 public/uploads/original/
chmod 755 public/uploads/format1/
chmod 755 public/uploads/format2/
chmod 644 public/uploads/*/*.webp

# Vérifier
ls -la public/uploads/
```

### Upload bloqué à "Upload en cours..."

**Cause :** Erreur JavaScript ou timeout

**Solution :**
1. Ouvrir la console navigateur (F12)
2. Regarder l'onglet "Network" pendant l'upload
3. Vérifier la réponse du serveur
4. Augmenter `max_execution_time` dans php.ini si timeout

### Images ne se réorganisent pas

**Cause :** JavaScript drag & drop non initialisé

**Solution :**
1. Vérifier console navigateur pour erreurs
2. Vérifier que Lucide icons est chargé
3. Recharger la page avec Ctrl+F5

### Ancienne image encore affichée

**Cause :** Cache navigateur

**Solution :**
```bash
# Vider cache CodeIgniter
php spark cache:clear

# Recharger page avec Ctrl+F5
```

---

## 📝 Checklist Finale

Avant de passer en production :

- [ ] Migration exécutée sans erreur
- [ ] 6 images uploadables par produit
- [ ] Upload drag & drop fonctionnel
- [ ] Réorganisation drag & drop fonctionnelle
- [ ] Image principale sélectionnable
- [ ] Suppression individuelle fonctionnelle
- [ ] Galerie visiteur affichée correctement
- [ ] Cards produits utilisent image principale
- [ ] Mobile responsive testé
- [ ] Permissions fichiers correctes (755/644)
- [ ] Logs vérifiés (pas d'erreurs critiques)
- [ ] Tests CASCADE delete validés
- [ ] Documentation à jour
- [ ] Commit Git avec message descriptif

---

## 🚀 Déploiement

### 1. Commit les modifications

```bash
git add .
git commit -m "✨ Implémentation système galerie multi-images (6 max)

- Migration table product_images avec FK CASCADE
- ProductImageModel avec 10 méthodes (CRUD + positions)
- ImageProcessor avec numérotation (SKU-format1-X.webp)
- API RESTful admin (5 endpoints)
- Interface drag & drop upload + réorganisation
- Galerie frontend horizontale
- Documentation complète MULTI_IMAGE_GALLERY.md
- Tests manuels validés
"
```

### 2. Push vers GitHub

```bash
git push origin main
```

### 3. Déploiement automatique

Le workflow `.github/workflows/deploy.yml` va :
1. Faire un backup de la BDD
2. Synchroniser les fichiers
3. Exécuter les migrations
4. Vider le cache
5. Redémarrer le serveur

### 4. Vérification post-déploiement

```bash
# Sur le serveur de production
ssh user@kayart-server

# Vérifier migration
php spark migrate:status

# Vérifier permissions
ls -la /var/www/kayart/public/uploads/

# Vérifier logs
tail -f /var/www/kayart/writable/logs/log-$(date +%Y-%m-%d).log
```

---

**Temps estimé :** 30-60 minutes pour toutes les étapes

**Support :** Voir `MULTI_IMAGE_GALLERY.md` pour la documentation complète
