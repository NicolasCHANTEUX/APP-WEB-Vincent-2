# 📚 GUIDE D'INSTALLATION ET D'UTILISATION DE LA BASE DE DONNÉES

## 🗄️ Configuration de la base de données

### Option 1 : Utilisation du script SQL complet (Recommandé)

Le fichier `app/Database/scripts/init_database.sql` contient toute la structure de la base de données ainsi que des données de démonstration.

**Étapes :**

1. **Créer la base de données** (si elle n'existe pas déjà) :
   ```bash
   mysql -u root -p -e "CREATE DATABASE boutique_en_ligne CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

2. **Importer le script SQL complet** :
   ```bash
   mysql -u root -p boutique_en_ligne < app/Database/scripts/init_database.sql
   ```

3. **Vérifier l'importation** :
   ```bash
   mysql -u root -p boutique_en_ligne -e "SHOW TABLES;"
   ```

### Option 2 : Utilisation des migrations CodeIgniter

Si vous préférez utiliser le système de migration intégré de CodeIgniter :

1. **Configurer le fichier .env** (voir section suivante)

2. **Exécuter les migrations** :
   ```bash
   php spark migrate
   ```

3. **Vérifier le statut des migrations** :
   ```bash
   php spark migrate:status
   ```

4. **Rollback si nécessaire** :
   ```bash
   php spark migrate:rollback
   ```

---

## ⚙️ Configuration du fichier .env

1. **Copier le fichier d'exemple** :
   ```bash
   cp env.example .env
   ```

2. **Éditer le fichier .env** et ajuster les paramètres de connexion :
   ```ini
   #--------------------------------------------------------------------
   # DATABASE
   #--------------------------------------------------------------------
   
   database.default.hostname = 127.0.0.1
   database.default.database = boutique_en_ligne
   database.default.username = root
   database.default.password = VOTRE_MOT_DE_PASSE_ICI
   database.default.DBDriver = MySQLi
   database.default.port = 3306
   ```

3. **Configurer SSL (si activé)** :
   ```ini
   # Décommenter et configurer si SSL est requis
   # database.default.encrypt.ssl_verify = true
   # database.default.encrypt.ssl_ca = /path/to/ca-cert.pem
   # database.default.encrypt.ssl_cipher = TLS_AES_256_GCM_SHA384
   ```

---

## 🗂️ Structure de la base de données

### Tables principales

| Table | Description | Relations |
|-------|-------------|-----------|
| `user` | Utilisateurs administrateurs | - |
| `category` | Catégories de produits | - |
| `product` | Produits (pagaies, sièges, etc.) | FK → `category` |
| `service` | Services proposés | - |
| `contact_request` | Demandes de contact | - |
| `contact_attachment` | Pièces jointes | FK → `contact_request` |
| `reservation` | Réservations clients | FK → `product` |

### Table RESERVATION (Objectif principal du projet)

C'est **LA TABLE CLÉ** pour le système de réservation avec contact humain :

```sql
CREATE TABLE `reservation` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_email` VARCHAR(255) NOT NULL,
  `customer_phone` VARCHAR(50),
  `message` TEXT,
  `quantity` INT DEFAULT 1,
  `status` ENUM('new', 'contacted', 'confirmed', 'completed', 'cancelled') DEFAULT 'new',
  `admin_notes` TEXT,
  `contacted_at` DATETIME,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME,
  FOREIGN KEY (`product_id`) REFERENCES `product`(`id`)
);
```

**Workflow typique :**
1. Client réserve un produit → `status = 'new'`
2. Admin voit la réservation et contacte le client → `status = 'contacted'`, `contacted_at` = now()
3. Discussion et confirmation → `status = 'confirmed'`
4. Transaction terminée → `status = 'completed'`

---

## 👤 Compte administrateur par défaut

Le script SQL crée automatiquement un compte admin :

- **Username** : `admin`
- **Email** : `admin@kayart.com`
- **Mot de passe** : `Admin123!`

⚠️ **À CHANGER IMMÉDIATEMENT EN PRODUCTION !**

### Générer un nouveau hash de mot de passe

```bash
php -r "echo password_hash('VotreNouveauMotDePasse', PASSWORD_DEFAULT) . PHP_EOL;"
```

Puis mettre à jour dans la table `user` ou dans `.env` :

```ini
ADMIN_EMAIL = admin@kayart.com
ADMIN_PASSWORD_HASH = '$2y$10$VotreLongHashIci...'
```

---

## 📊 Données de démonstration

Le script SQL inclut :

- ✅ 4 catégories (Pagaies, Sièges, Cales, Accessoires)
- ✅ 5 produits (pagaies carbone, sièges)
- ✅ 3 services (Réparation, Personnalisation, Conseil)
- ✅ 1 utilisateur admin

Ces données permettent de tester immédiatement l'application.

---

## 🔧 Commandes utiles

### Connexion MySQL

```bash
mysql -u root -p -h 127.0.0.1 -P 3306
```

### Vérifier la structure

```sql
USE boutique_en_ligne;
SHOW TABLES;
DESCRIBE product;
DESCRIBE reservation;
```

### Voir les produits

```sql
SELECT * FROM product;
```

### Voir les réservations avec détails produit

```sql
SELECT * FROM v_reservations_with_product;
```

### Réinitialiser complètement la base

```bash
mysql -u root -p -e "DROP DATABASE IF EXISTS boutique_en_ligne;"
mysql -u root -p -e "CREATE DATABASE boutique_en_ligne CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p boutique_en_ligne < app/Database/scripts/init_database.sql
```

---

## 🚀 Prochaines étapes

Maintenant que la base de données est configurée, vous pouvez :

1. ✅ Créer les **Modèles** CodeIgniter (ProductModel, ReservationModel, etc.)
2. ✅ Implémenter le **formulaire de réservation** côté public
3. ✅ Créer l'**interface admin** pour gérer les réservations
4. ✅ Finaliser le **CRUD des produits** côté admin
5. ✅ Optimiser le **SEO** et les performances

---

## ❓ Informations manquantes

Si vous avez configuré SSL pour MySQL avec des certificats spécifiques, veuillez fournir :
- Chemin du certificat CA (`ssl_ca`)
- Chemin du certificat client (`ssl_cert`)
- Chemin de la clé privée (`ssl_key`)

Ces informations seront à ajouter dans le fichier `.env`.
