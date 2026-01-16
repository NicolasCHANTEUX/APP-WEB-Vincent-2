# 📋 To-Do List : Améliorations & Correctifs KayArt

## ✅ Tâches Complétées

### 1. Informations Générales & Sécurité
- [x] **Confidentialité de l'adresse** (Footer + Contact)
  - ✅ Adresse remplacée par "La Ferté-Bernard, Sarthe (72)" dans les fichiers de langue FR et EN
  - ✅ Protection de l'adresse personnelle complète

### 2. Emails Transactionnels & Notifications
- [x] **Correction calcul TVA dans emails de commande**
  - ✅ Corrigé le calcul: `totalHT = totalTTC / 1.20` et `TVA = totalTTC - totalHT`
  - ✅ La ligne TVA affiche maintenant le bon montant au lieu de 0,00 €
  - Fichier modifié: `app/Controllers/CheckoutController.php` (lignes 521-524, 573-575)

---

## 🔧 Tâches Restantes

### 1. Informations Générales & Sécurité

#### Google Map / OpenStreetMap
- [ ] Mettre à jour les coordonnées GPS pour pointer vers le centre-ville de La Ferté-Bernard au lieu du domicile exact
- [ ] Fichiers concernés: Pages Contact / Footer (si carte intégrée)
- **Impact**: Sécurité - Éviter la divulgation de l'adresse personnelle

---

### 2. Gestion des Images (Produits & Upload)

#### Correction du Rognage (Crop)
- [ ] Vérifier le script `ImageProcessor` lors de l'upload
  - Chemin: `app/Libraries/ImageProcessor.php`
  - Problème: Les miniatures coupent parfois le produit de manière disgracieuse
  - Solutions possibles:
    * Ajuster le ratio de redimensionnement (actuellement 1920×?, 800×?, 350×?)
    * Modifier `object-fit: cover` → `object-fit: contain` dans le CSS
    * Ajouter un padding blanc pour conserver les proportions

- [ ] Vérifier le CSS d'affichage sur la fiche produit
  - Fichiers: `app/Views/components/section/produits/*.php`
  - Vérifier les classes Tailwind: `object-cover`, `object-contain`, `aspect-ratio`

#### Correction de l'Orientation (Rotation 90°)
- [ ] Implémenter la lecture des données **EXIF** lors de l'upload
  - Problème: Photos iPhone/Android peuvent avoir une mauvaise orientation
  - Solution: Détecter `exif_read_data()` et effectuer rotation automatique avant sauvegarde
  - Fichier à modifier: `app/Libraries/ImageProcessor.php` (dans la méthode `processProductImage`)
  
  **Code suggéré**:
  ```php
  // Après le move() de l'image
  $exif = @exif_read_data($uploadedPath);
  if ($exif && isset($exif['Orientation'])) {
      $image = imagecreatefromjpeg($uploadedPath);
      switch ($exif['Orientation']) {
          case 3:
              $image = imagerotate($image, 180, 0);
              break;
          case 6:
              $image = imagerotate($image, -90, 0);
              break;
          case 8:
              $image = imagerotate($image, 90, 0);
              break;
      }
      imagejpeg($image, $uploadedPath, 90);
      imagedestroy($image);
  }
  ```

---

### 3. Emails Transactionnels & Notifications

#### Refonte Visuelle "Demandes de Contact"
- [ ] Créer un template HTML pour les emails reçus via le formulaire de contact
  - Actuellement: Texte brut sans style
  - Objectif: Appliquer la charte graphique (Logo, couleurs KayArt)
  - Fichiers concernés:
    * Contrôleur: `app/Controllers/ContactControler.php` (méthode d'envoi email)
    * Créer un template similaire à `buildOrderEmailTemplate()`
  - Inspiration: Réutiliser le design des emails de commande

#### Internationalisation (Logique Langue)
- [ ] Détection automatique de la langue pour l'email de confirmation de commande
  - **Règle**: Si téléphone commence par `+33` → Français, sinon → Anglais
  - Alternative: Détecter la langue du navigateur/session lors de la commande
  - Fichier à modifier: `app/Controllers/CheckoutController.php` (méthode `buildOrderEmailTemplate`)
  - Créer deux versions du template (FR/EN) ou utiliser `trans()` dans le template

  **Code suggéré**:
  ```php
  // Dans buildOrderEmailTemplate()
  $phone = $customerData['phone'] ?? '';
  $emailLang = (str_starts_with($phone, '+33') || str_starts_with($phone, '0')) ? 'fr' : 'en';
  
  // Ensuite utiliser $emailLang pour charger les traductions
  helper('language');
  $locale = service('request')->getLocale();
  // Temporairement changer la locale pour l'email
  service('request')->setLocale($emailLang);
  ```

#### Correction Icônes Réseaux Sociaux dans les Emails
- [ ] Réparer l'affichage des logos (Instagram, Facebook, LinkedIn)
  - **Problème actuel**: Utilisation d'emojis (📘 📸) au lieu d'images
  - **Cause**: Les images SVG ne sont pas supportées par tous les clients mail
  - **Solution**: Utiliser des PNG hébergés avec liens absolus `https://...`
  
  **Actions**:
  1. Créer des icônes PNG (32×32 ou 48×48) pour chaque réseau social
  2. Les placer dans `public/images/social/` (facebook.png, instagram.png, linkedin.png)
  3. Les référencer avec `base_url('images/social/facebook.png')`
  4. Remplacer les emojis dans le template email (lignes 676-693 de CheckoutController.php)
  
  **Code suggéré**:
  ```html
  <a href="https://facebook.com/kayart">
      <img src="<?= base_url('images/social/facebook.png') ?>" 
           alt="Facebook" 
           style="width: 32px; height: 32px; display: block;" />
  </a>
  ```

---

## 📝 Notes Techniques

### Priorité des tâches
1. **Critique** (Sécurité): Coordonnées GPS Google Map ⚠️
2. **Haute** (UX): Orientation EXIF photos + Crop images
3. **Moyenne** (Emails): Templates contact + Icônes réseaux sociaux
4. **Basse** (i18n): Internationalisation emails

### Fichiers principaux concernés
- `app/Libraries/ImageProcessor.php` (gestion images)
- `app/Controllers/CheckoutController.php` (emails commande) ✅ Partiellement fait
- `app/Controllers/ContactControler.php` (emails contact)
- `app/Language/fr/Texts.php` ✅ Fait
- `app/Language/en/Texts.php` ✅ Fait

### Extensions PHP requises
- `php-exif` (pour lecture orientation images)
- `php-gd` (pour rotation images)

### Tests à effectuer après corrections
- [ ] Upload d'une photo iPhone en portrait (EXIF Orientation 6 ou 8)
- [ ] Vérification du rendu email sur Gmail, Outlook, Apple Mail
- [ ] Test du calcul TVA sur plusieurs commandes ✅ À tester
- [ ] Vérification de l'affichage responsive des produits

---

**Dernière mise à jour**: 16 janvier 2026
**Tâches complétées**: 2/9 (22%)
