# Gestion des Demandes - Documentation

## 📋 Vue d'ensemble

L'application gère **deux types de demandes distinctes** :

### 1️⃣ **Demandes de Contact** (Admin > Demandes) 
- **Table** : `contact_request`
- **Source** : Formulaire de contact général du site
- **Contenu** : Questions générales, demandes d'information, suggestions
- **Champs clés** : `name`, `email`, `subject`, `message`
- **Statuts** : `new`, `in_progress`, `completed`, `archived`
- **Accessible via** : `/admin/demandes`

### 2️⃣ **Réservations de Produits** (À venir)
- **Table** : `reservation`
- **Source** : Bouton "Réserver" sur les fiches produits
- **Contenu** : Demandes liées à un produit spécifique
- **Champs clés** : `product_id`, `customer_name`, `customer_email`, `customer_phone`, `message`, `quantity`
- **Statuts** : `new`, `contacted`, `confirmed`, `completed`, `cancelled`
- **Accessible via** : `/admin/reservations` (futur)

---

## 🔄 Changements effectués

### Modèles
- ✅ **Créé** : `ContactRequestModel.php` - Gère les demandes de contact générales
- 📦 **Existant** : `ReservationModel.php` - Gère les réservations de produits (sera utilisé plus tard)

### Contrôleurs
- ✅ **`AdminDemandesController.php`** - Gère les demandes de contact (`contact_request`)
  - `index()` - Liste des demandes
  - `show($id)` - Détail d'une demande
  - `updateStatus($id)` - Mise à jour du statut et réponse
- 📦 **`AdminReservationsController.php`** - Gère les réservations de produits (ancien, à supprimer ou renommer)

### Vues - Section Admin > Demandes
- ✅ **`pages/admin/demandes.php`** - Page liste des demandes de contact
- ✅ **`pages/admin/demande_detail.php`** - Page détail d'une demande
- ✅ **`components/section/admin/demandes_section.php`** - Tableau des demandes
- ✅ **`components/section/admin/demande_detail.php`** - Vue détaillée avec formulaire de traitement

### Routes
```php
// Gestion des Demandes de contact
$routes->get('admin/demandes', 'AdminDemandesController::index');
$routes->get('admin/demandes/(:num)', 'AdminDemandesController::show/$1');
$routes->post('admin/demandes/(:num)/status', 'AdminDemandesController::updateStatus/$1');

// Gestion des Réservations de produits (à implémenter)
// $routes->get('admin/reservations', 'AdminReservationsController::index');
```

---

## 📊 Structure des données

### Table `contact_request`
```sql
CREATE TABLE `contact_request` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('new', 'in_progress', 'completed', 'archived') DEFAULT 'new',
  `admin_reply` TEXT DEFAULT NULL,
  `replied_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

### Table `reservation`
```sql
CREATE TABLE `reservation` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_email` VARCHAR(255) NOT NULL,
  `customer_phone` VARCHAR(50) DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `quantity` INT DEFAULT 1,
  `status` ENUM('new', 'contacted', 'confirmed', 'completed', 'cancelled') DEFAULT 'new',
  `admin_notes` TEXT DEFAULT NULL,
  `contacted_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
);
```

---

## 🎨 Interface Admin - Demandes de Contact

### Liste (`/admin/demandes`)
Affiche toutes les demandes de contact avec :
- **Filtres par statut** : Nouvelles, En cours, Traitées, Archivées
- **Colonnes** : Client, Sujet, Extrait du message, Statut, Date, Actions
- **Actions** : Voir détail, Modifier statut

### Détail (`/admin/demandes/{id}`)
Affiche une demande complète avec :
- Sujet de la demande
- Message complet du client
- Informations client (nom, email)
- Réponse admin (si déjà envoyée)
- Formulaire de traitement :
  - Sélection du statut
  - Zone de texte pour la réponse
  - Bouton "Mettre à jour"
- Lien "Répondre par email" (ouvre le client mail)

---

## 📈 Statistiques Dashboard

Le tableau de bord affiche :
- **Nouvelles demandes** : Compte des `contact_request` avec `status = 'new'`
- Lien cliquable vers `/admin/demandes`

---

## 🔜 Prochaines étapes

### Pour les Réservations de Produits
1. Créer `AdminReservationsController` (distinct de AdminDemandesController)
2. Créer les vues pour `/admin/reservations`
3. Afficher les infos produit (titre, prix, image, stock)
4. Gérer la quantité et les notes admin
5. Ajouter les routes dédiées

### Améliorations possibles
- Système de notification email automatique
- Filtres avancés (recherche, date range)
- Export CSV des demandes
- Templates de réponses prédéfinis
- Archivage automatique après X jours

---

## ⚠️ Important

**Ne pas confondre** :
- 📧 **Demandes** = Contact général (`contact_request`)
- 🛒 **Réservations** = Demandes liées à un produit (`reservation`)

Les deux utilisent des tables, modèles et interfaces différents !

