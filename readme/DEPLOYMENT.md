# 🚀 Guide de Déploiement KayArt

## Prérequis Serveur

### Logiciels requis
- **PHP 8.1+** avec extensions :
  - gd (traitement d'images)
  - intl (internationalisation)
  - mbstring (chaînes multi-octets)
  - mysqli (base de données)
  - curl (requêtes HTTP)
  - zip (compression)
  - fileinfo (détection type MIME)
  - json, xml, dom
  
- **MySQL/MariaDB 8.0+**
- **Composer 2.x**
- **Node.js 18+** et NPM
- **Apache 2.4+** ou **Nginx**
- **Git**

### Vérification rapide
```bash
bash scripts/check-requirements.sh
```

## 📋 Installation Initiale

### 1. Cloner le repository
```bash
cd /var/www
sudo git clone https://github.com/NicolasCHANTEUX/APP-WEB-Vincent-2.git kayart
cd kayart
```

### 2. Configuration de l'environnement
```bash
# Copier le fichier .env d'exemple
cp .env.example .env

# Éditer avec vos paramètres
nano .env

# Générer la clé d'encryption
php spark key:generate
```

### 3. Base de données
```bash
# Créer la base de données
mysql -u root -p
CREATE DATABASE kayart_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kayart_user'@'localhost' IDENTIFIED BY 'VOTRE_MOT_DE_PASSE';
GRANT ALL PRIVILEGES ON kayart_db.* TO 'kayart_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Exécuter les migrations
php spark migrate
```

### 4. Dépendances
```bash
# PHP
composer install --no-dev --optimize-autoloader

# JavaScript
npm install
npm run build
```

### 5. Permissions
```bash
# Créer les dossiers
sudo mkdir -p writable/{cache,logs,session,debugbar,uploads}
sudo mkdir -p public/uploads
sudo mkdir -p public/writable/session
sudo mkdir -p writable/uploads/invoices

# Permissions
sudo chown -R www-data:www-data writable
sudo chown -R www-data:www-data public/uploads
sudo chown -R www-data:www-data public/writable
sudo chmod -R 755 writable
sudo chmod -R 755 public/uploads
sudo chmod -R 755 public/writable
```

### 6. Configuration Apache/Nginx

#### Apache (.htaccess déjà présent)
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx (exemple de configuration)
```nginx
server {
    listen 80;
    server_name kayart.fr www.kayart.fr;
    root /var/www/kayart/public;
    
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
```

## 🔄 Déploiement Automatique (GitHub Actions)

### Configuration du runner self-hosted

1. **Installer le runner GitHub**
```bash
cd /home/votre-user
mkdir actions-runner && cd actions-runner
# Télécharger et installer selon les instructions GitHub
```

2. **Le déploiement se fait automatiquement** à chaque push sur `main`

### Processus de déploiement
1. ✅ Vérification de l'environnement PHP
2. ✅ Build de Tailwind CSS
3. ✅ Backup de la base de données
4. ✅ Copie des fichiers
5. ✅ Installation des dépendances
6. ✅ Configuration des permissions
7. ✅ Nettoyage du cache
8. ✅ Migration de la BDD
9. ✅ Redémarrage du serveur web

## 🔐 Secrets à Configurer

### Dans le fichier .env
- `database.default.password` : Mot de passe MySQL
- `stripe.publishableKey` : Clé publique Stripe
- `stripe.secretKey` : Clé secrète Stripe
- `stripe.webhookSecret` : Secret webhook Stripe
- `email.SMTPPass` : App password Gmail
- `encryption.key` : Généré avec `php spark key:generate`

## 📊 Maintenance

### Backup manuel de la BDD
```bash
mysqldump -u kayart_user -p kayart_db > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Nettoyage du cache
```bash
cd /var/www/kayart
php spark cache:clear
sudo rm -rf writable/cache/*
sudo rm -rf writable/debugbar/*
```

### Logs
```bash
# Voir les logs d'erreur
tail -f writable/logs/log-$(date +%Y-%m-%d).log

# Nettoyer les vieux logs (>30 jours)
find writable/logs -name "log-*.log" -mtime +30 -delete
```

### Vérifier les migrations
```bash
php spark migrate:status
```

## 🐛 Dépannage

### Erreur 500
1. Vérifier les logs : `tail -f writable/logs/log-$(date +%Y-%m-%d).log`
2. Vérifier les permissions : `ls -la writable`
3. Vérifier le .env : `cat .env | grep -v password`

### Images ne s'affichent pas
1. Vérifier les permissions : `ls -la public/uploads`
2. Vérifier l'extension GD : `php -m | grep gd`

### Emails ne partent pas
1. Tester la config SMTP : `php spark email:test`
2. Vérifier les logs : `grep -i "email" writable/logs/*.log`
3. Vérifier le app password Gmail

### Migration échoue
1. Voir le statut : `php spark migrate:status`
2. Rollback : `php spark migrate:rollback`
3. Re-migrer : `php spark migrate`

## 📞 Support

- Email : contact.kayart@gmail.com
- GitHub Issues : https://github.com/NicolasCHANTEUX/APP-WEB-Vincent-2/issues
