# APP_WEB_Vincent

Application Web développée avec CodeIgniter 4 et Tailwind CSS.

## Prérequis

- PHP 8.1 ou supérieur
- Composer
- Node.js et npm
- Git

## Installation

1. **Cloner le projet** (si nécessaire)
   ```bash
   git clone <repository-url>
   cd APP_WEB_Vincent
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Installer les dépendances Node.js**
   ```bash
   npm install
   ```

4. **Compiler Tailwind CSS**
   ```bash
   npm run build
   ```

## Développement

### Démarrer le serveur de développement CodeIgniter
```bash
php spark serve
```
L'application sera accessible sur `http://localhost:8080`

### Compiler Tailwind CSS en mode développement (avec watch)
```bash
npm run dev
```

### Compiler Tailwind CSS pour la production
```bash
npm run build
```

## Structure du projet

- `app/` - Code de l'application CodeIgniter (Contrôleurs, Modèles, Vues)
- `public/` - Fichiers publics accessibles via le web
  - `css/input.css` - Fichier source Tailwind
  - `css/output.css` - Fichier compilé Tailwind
- `vendor/` - Dépendances PHP installées par Composer
- `node_modules/` - Dépendances Node.js installées par npm

## Internationalisation (FR/EN)

- **Langue via l’URL** : `?lang=fr` ou `?lang=en` (recommandé)
- **Alias** : `/<page>/lang/fr` ou `/<page>/lang/en` redirige vers la version `?lang=...`
- **Persistance** : la langue est mémorisée en cookie `site_lang` (et le bouton de la navbar garde l’état via `localStorage`).
- **Traductions** : helper `trans()` + fichiers `app/Language/{fr,en}/Texts.php`.
- **Compat** : les anciennes URLs `/fr/...` et `/en/...` redirigent vers la nouvelle structure.

## Construction des vues (comme le projet exemple)

- **Pages** : `app/Views/pages/*`
- **Layout** : `app/Views/layouts/root_layout.php`
- **Composants** : `app/Views/components/*` et `app/Views/components/section/*`
- **Container responsive** : `app/Views/partager/container.php`, rendu via `view_cell('App\\Cells\\ContainerComposant::render', ...)`
- **Cells** : `app/Cells/*` et `app/Cells/sections/*` pour composer les pages comme dans l’exemple

## Configuration Tailwind CSS

Tailwind est configuré pour scanner :
- Tous les fichiers PHP dans `app/Views/`
- Tous les fichiers PHP dans `app/Controllers/`
- Tous les fichiers HTML dans `public/`

Le fichier de configuration se trouve dans `tailwind.config.js`.

## Commandes utiles

- `php spark` - Liste des commandes CodeIgniter disponibles
- `composer test` - Exécuter les tests
- `npm run dev` - Développement avec rechargement automatique
- `npm run build` - Build de production

## Fonctionnalités

- Framework CodeIgniter 4 avec architecture MVC
- Tailwind CSS pour le styling
- Structure prête pour le développement
- Tests unitaires configurés
- Serveur de développement intégré

## Déploiement

1. Compiler les assets pour la production :
   ```bash
   npm run build
   ```

2. Configurer votre serveur web pour pointer vers le dossier `public/`

3. **Configurer les variables d'environnement dans `.env`**

   Créez un fichier `.env` à la racine du projet (ou copiez depuis `env` si disponible) et configurez :
   
   ```env
   # Base URL de l'application
   app.baseURL = 'http://localhost:8080/'
   
   # Configuration de l'authentification admin
   ADMIN_EMAIL=admin@example.com
   
   # Mot de passe hashé de l'administrateur
   # Pour générer un hash, utilisez: php -r "echo password_hash('votre_mot_de_passe', PASSWORD_DEFAULT);"
   ADMIN_PASSWORD_HASH=$2y$10$Rsj5hTed8zAigD7P723lWOSYO/WlTdMz6fATcW8CeZRjJC0LRGmbi
   ```
   
   **Note** : Remplacez `ADMIN_EMAIL` et `ADMIN_PASSWORD_HASH` par vos propres valeurs. Le hash du mot de passe doit être généré avec `password_hash()` de PHP.
