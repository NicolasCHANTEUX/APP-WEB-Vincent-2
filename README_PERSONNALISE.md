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

3. Configurer les variables d'environnement dans `env` si nécessaire
