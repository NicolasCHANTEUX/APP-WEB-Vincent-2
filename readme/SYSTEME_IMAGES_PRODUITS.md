# Système de Traitement d'Images Produits

## 📁 Structure des Fichiers

```
APP_WEB_Vincent/
├── app/
│   ├── Controllers/
│   │   └── AdminProduitsController.php  # Gestion CRUD produits avec images
│   └── Libraries/
│       └── ImageProcessor.php            # Service de traitement d'images
└── writable/
    └── uploads/
        ├── .htaccess                     # Sécurité (pas de PHP exécutable)
        ├── original/                     # Version haute qualité (1920px, 90%)
        ├── format1/                      # Fiche produit (800px, 85%)
        └── format2/                      # Grille/miniature (400px, 80%)
```

## 🔄 Flux de Traitement

### 1. Upload d'une Image Produit

Lorsqu'un admin crée/modifie un produit avec une image :

1. **Validation** :
   - Types acceptés : JPEG, PNG, WebP
   - Taille max : 10 MB
   - SKU requis et unique

2. **Traitement Automatique** (`ImageProcessor::processProductImage()`) :
   - Renommage avec SKU du produit
   - Conversion en WebP
   - Génération de 3 versions :

| Version | Dossier | Dimensions | Qualité | Nom Fichier | Usage |
|---------|---------|------------|---------|-------------|-------|
| Original | `original/` | Max 1920px | 90% | `{sku}.webp` | Zoom, haute qualité |
| Format1 | `format1/` | Max 800px | 85% | `{sku}-format1.webp` | Fiche produit |
| Format2 | `format2/` | Max 400px | 80% | `{sku}-format2.webp` | Grille, miniature |

3. **Stockage Base de Données** :
   - Colonne `image` : contient uniquement `{sku}.webp`
   - Les 3 versions sont déduites automatiquement

### 2. Modification d'Image

Lors du remplacement d'une image :

1. Suppression des 3 anciennes versions
2. Génération de 3 nouvelles versions avec le même SKU

### 3. Suppression de Produit

- Suppression automatique des 3 versions d'image
- Puis suppression de l'enregistrement en BDD

## 🛠️ Utilisation dans le Code

### Créer un Produit avec Image

```php
// AdminProduitsController::create()
$imageFile = $this->request->getFile('image');
$result = $this->imageProcessor->processProductImage($imageFile, $data['sku']);

if ($result['success']) {
    $data['image'] = $result['filename']; // Ex: "PAG-CARB-001.webp"
    $this->productModel->insert($data);
}
```

### Afficher une Image dans une Vue

```php
use App\Libraries\ImageProcessor;

$imageProcessor = new ImageProcessor();

// Version grille (miniature)
$thumbUrl = $imageProcessor->getImageUrl($product['image'], 'format2');

// Version fiche produit
$detailUrl = $imageProcessor->getImageUrl($product['image'], 'format1');

// Version originale (zoom)
$fullUrl = $imageProcessor->getImageUrl($product['image'], 'original');
```

Exemple d'affichage HTML :

```html
<!-- Grille de produits -->
<img src="<?= $imageProcessor->getImageUrl($product['image'], 'format2') ?>" 
     alt="<?= esc($product['title']) ?>">

<!-- Fiche produit -->
<img src="<?= $imageProcessor->getImageUrl($product['image'], 'format1') ?>" 
     alt="<?= esc($product['title']) ?>"
     onclick="showFullImage('<?= $imageProcessor->getImageUrl($product['image'], 'original') ?>')">
```

## 📋 Logs de Debugging

Tous les traitements génèrent des logs détaillés dans `writable/logs/` :

```
[ImageProcessor] === DÉBUT TRAITEMENT IMAGE ===
[ImageProcessor] SKU: PAG-CARB-001
[ImageProcessor] Fichier source: pagaie.jpg
[ImageProcessor] Type MIME: image/jpeg
[ImageProcessor] Taille: 2458963 bytes
[ImageProcessor] SKU nettoyé: PAG-CARB-001
[ImageProcessor] Génération original : writable/uploads/original/PAG-CARB-001.webp
[ImageProcessor] ✓ original généré : PAG-CARB-001.webp (485632 bytes)
[ImageProcessor] Génération format1 : writable/uploads/format1/PAG-CARB-001-format1.webp
[ImageProcessor] ✓ format1 généré : PAG-CARB-001-format1.webp (124856 bytes)
[ImageProcessor] Génération format2 : writable/uploads/format2/PAG-CARB-001-format2.webp
[ImageProcessor] ✓ format2 généré : PAG-CARB-001-format2.webp (32145 bytes)
[ImageProcessor] === SUCCÈS : 3 versions générées ===
```

## ⚠️ Gestion d'Erreurs

Le système gère automatiquement :

- Type de fichier invalide → Message d'erreur explicite
- Fichier trop volumineux → Refus avec message
- Erreur de conversion → Exception capturée et loggée
- SKU invalide → Nettoyage automatique (caractères alphanumériques uniquement)
- Image inexistante lors suppression → Log informatif, pas d'erreur

## 🔐 Sécurité

- `.htaccess` empêche l'exécution de scripts PHP dans `/uploads/`
- Validation stricte des types MIME
- Nettoyage du SKU (pas d'injection de path)
- Taille max 10 MB par fichier

## 🎯 Avantages

1. **Performance** :
   - Images WebP (30-50% plus légères que JPEG)
   - Versions adaptées à l'usage (pas de 5MB pour une miniature)
   - Chargement progressif possible

2. **Maintenabilité** :
   - Nommage cohérent basé sur SKU
   - Logs détaillés pour debugging
   - Code centralisé dans ImageProcessor

3. **Expérience Utilisateur** :
   - Chargement rapide des grilles
   - Haute qualité pour le zoom
   - Conversion automatique (admin n'a pas besoin de préparer les images)

## 📝 TODO / Améliorations Futures

- [ ] Support de plusieurs images par produit (galerie)
- [ ] Génération de formats responsive (`srcset`)
- [ ] Watermark automatique
- [ ] Compression AVIF en plus de WebP
- [ ] Interface d'édition d'images (crop, rotation)
