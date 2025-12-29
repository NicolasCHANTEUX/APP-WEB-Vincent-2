# Optimisations de performances réalisées

## ✅ Optimisations CODE implémentées

### 1. Cache HTTP configuré (public/.htaccess)
**Impact : Visites répétées beaucoup plus rapides**

- ✅ Headers `Cache-Control` ajoutés pour :
  - Images : 1 an de cache (`max-age=31536000, immutable`)
  - CSS/JS : 1 mois de cache
  - Fonts : 1 an de cache
  - HTML : pas de cache (pour contenu dynamique)
- ✅ Support du **bfcache** (Back/Forward cache) activé
- ✅ Headers `Expires` configurés via `mod_expires`

### 2. Debugbar désactivée en production
**Impact : Réduit drastiquement le JS/CSS chargé**

- ✅ Documentation ajoutée dans `.env` :
  ```
  # En production, mettre 'production' pour désactiver debugbar
  CI_ENVIRONMENT = development
  ```
- ✅ Par défaut, CodeIgniter désactive automatiquement la toolbar si `CI_ENVIRONMENT = production`

### 3. Tailwind CSS optimisé
**Impact : CSS beaucoup plus léger**

- ✅ Configuration `tailwind.config.js` déjà configurée avec purge :
  ```js
  content: [
    './app/Views/**/*.php',
    './app/Controllers/**/*.php',
  ]
  ```
- ✅ Script de build avec minification : `npm run build`
- ✅ Commande à exécuter en production : 
  ```bash
  npm run build
  ```

### 4. Polices Google optimisées (root_layout.php)
**Impact : Améliore le rendu du texte**

- ✅ `display=swap` déjà présent dans l'URL Google Fonts
- ✅ Preload du CSS ajouté : `<link rel="preload" href="/css/output.css" as="style">`
- ✅ Preconnect maintenu pour Google Fonts

### 5. Images optimisées pour CLS (Cumulative Layout Shift)
**Impact : Empêche le "saut" des éléments pendant le chargement**

Fichiers modifiés :
- ✅ **product_card.php** : `width="400" height="300"` ajoutés
- ✅ **hero.php** : `width="1920" height="1080"` ajoutés
- ✅ **product_detail_content.php** : `width="800" height="800"` ajoutés

### 6. Lazy loading activé
**Impact : Charge uniquement les images visibles à l'écran**

- ✅ `loading="lazy"` sur toutes les images **sauf** :
  - Image hero (LCP) avec `fetchpriority="high"`
- ✅ Images produits chargées progressivement au scroll

### 7. Responsive images avec srcset/sizes
**Impact : Charge la bonne taille d'image selon l'écran**

- ✅ **product_card.php** :
  ```html
  srcset="... 400w, ... 800w"
  sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
  ```
- ✅ **product_detail_content.php** :
  ```html
  srcset="... 400w, ... 800w, ... 1200w"
  sizes="(max-width: 1024px) 100vw, 50vw"
  ```

---

## 📸 Optimisations IMAGES à faire manuellement

### 🔴 Actions critiques restantes

#### 1. Redimensionner les images à la bonne taille
**Actuellement** : Images ~2000px affichées en 400px  
**À faire** :
- Générer 3 versions de chaque image produit :
  - `produit-small.webp` (400px)
  - `produit-medium.webp` (800px)
  - `produit-large.webp` (1200px)
- Utiliser un outil comme :
  - ImageMagick : `convert image.jpg -resize 400x image-small.webp`
  - Squoosh.app (en ligne)
  - Sharp (Node.js)

#### 2. Compresser les images WebP
**À faire** :
- Réduire la qualité WebP à 75-85% (au lieu de 95-100%)
- Utiliser `cwebp` :
  ```bash
  cwebp -q 80 input.jpg -o output.webp
  ```

#### 3. Convertir les PNG en WebP/SVG
**À faire** :
- Logos → SVG si possible
- Photos → WebP
- Éviter les PNG pour les photos

#### 4. Précharger l'image LCP (image hero)
**À faire dans root_layout.php** :
```html
<link rel="preload" as="image" href="/images/image_here.webp">
```

---

## 🎯 Checklist avant mise en production

- [ ] **Exécuter `npm run build`** pour minifier Tailwind CSS
- [ ] **Changer `.env`** : `CI_ENVIRONMENT = production`
- [ ] **Redimensionner et compresser toutes les images**
- [ ] **Générer versions responsive (400w, 800w, 1200w)**
- [ ] **Tester avec Lighthouse** (score attendu : 90+)
- [ ] **Vérifier le cache** : ouvrir DevTools > Network > recharger 2 fois
  - 1ère visite : 200 OK
  - 2ème visite : 304 Not Modified ou (from disk cache)

---

## 📊 Gains attendus après optimisation complète

| Métrique | Avant | Après (estimé) |
|----------|-------|----------------|
| **LCP** (Largest Contentful Paint) | ~4s | < 2.5s ✅ |
| **CLS** (Cumulative Layout Shift) | 0.1-0.25 | < 0.1 ✅ |
| **FCP** (First Contentful Paint) | ~2s | < 1.8s ✅ |
| **Taille CSS** | ~500 KB | ~50 KB ✅ |
| **Taille images** | ~2 MB | ~200 KB ✅ |

---

## 🛠️ Commandes utiles

### Construire le CSS optimisé
```bash
npm run build
```

### Vérifier la taille du CSS
```bash
# Windows PowerShell
(Get-Item public/css/output.css).length / 1KB
```

### Tester en local avec production
```bash
# Modifier .env temporairement
CI_ENVIRONMENT = production

# Redémarrer le serveur
php spark serve
```

---

## 📚 Ressources utiles

- **Lighthouse** : https://pagespeed.web.dev/
- **WebP Converter** : https://squoosh.app/
- **ImageMagick** : https://imagemagick.org/
- **Sharp (Node.js)** : https://sharp.pixelplumbing.com/
- **Core Web Vitals** : https://web.dev/vitals/
