-- ============================================================
-- Script d'initialisation de la base de données
-- Projet: APP_WEB_Vincent (Kayak - Fabrication artisanale)
-- Base de données: boutique_en_ligne
-- Date: 2025-12-29
-- ============================================================

-- Création de la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS `boutique_en_ligne` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `boutique_en_ligne`;

-- ============================================================
-- Table: user
-- Description: Utilisateurs administrateurs du système
-- ============================================================
CREATE TABLE IF NOT EXISTS `user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'editor') DEFAULT 'editor',
  `last_login` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `unique_email` (`email`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: category
-- Description: Catégories de produits (pagaies, sièges, etc.)
-- ============================================================
CREATE TABLE IF NOT EXISTS `category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_slug` (`slug`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: product
-- Description: Produits artisanaux (pagaies, sièges, cales, etc.)
-- ============================================================
CREATE TABLE IF NOT EXISTS `product` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `discounted_price` DECIMAL(10, 2) DEFAULT NULL,
  `weight` DECIMAL(10, 2) DEFAULT NULL COMMENT 'Poids en kg',
  `dimensions` VARCHAR(50) DEFAULT NULL COMMENT 'Ex: 210cm x 18cm',
  `image` VARCHAR(255) DEFAULT NULL,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `stock` INT DEFAULT 0,
  `sku` VARCHAR(50) NOT NULL COMMENT 'Référence produit unique',
  `condition_state` ENUM('new', 'used') DEFAULT 'new',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_slug` (`slug`),
  UNIQUE KEY `unique_sku` (`sku`),
  KEY `idx_category` (`category_id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_stock` (`stock`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) 
    REFERENCES `category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: service
-- Description: Services proposés (réparation, personnalisation, etc.)
-- ============================================================
CREATE TABLE IF NOT EXISTS `service` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(10, 2) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: contact_request
-- Description: Demandes de contact via le formulaire
-- ============================================================
CREATE TABLE IF NOT EXISTS `contact_request` (
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
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: contact_attachment
-- Description: Pièces jointes des demandes de contact
-- ============================================================
CREATE TABLE IF NOT EXISTS `contact_attachment` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `contact_request_id` INT UNSIGNED NOT NULL,
  `filename` VARCHAR(255) NOT NULL COMMENT 'Nom du fichier sur le serveur',
  `original_filename` VARCHAR(255) NOT NULL COMMENT 'Nom du fichier original',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contact_request` (`contact_request_id`),
  CONSTRAINT `fk_attachment_contact` FOREIGN KEY (`contact_request_id`) 
    REFERENCES `contact_request` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: reservation
-- Description: Réservations de produits par les clients
-- (Contact humain avant achat - objectif principal du projet)
-- ============================================================
CREATE TABLE IF NOT EXISTS `reservation` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_email` VARCHAR(255) NOT NULL,
  `customer_phone` VARCHAR(50) DEFAULT NULL,
  `message` TEXT DEFAULT NULL COMMENT 'Message/demande spécifique du client',
  `quantity` INT DEFAULT 1,
  `status` ENUM('new', 'contacted', 'confirmed', 'completed', 'cancelled') DEFAULT 'new',
  `admin_notes` TEXT DEFAULT NULL COMMENT 'Notes internes de l\'admin',
  `contacted_at` DATETIME DEFAULT NULL COMMENT 'Date du premier contact admin',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_status` (`status`),
  KEY `idx_customer_email` (`customer_email`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_reservation_product` FOREIGN KEY (`product_id`) 
    REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DONNÉES DE DÉMONSTRATION
-- ============================================================

-- Insertion d'un utilisateur admin par défaut
-- Username: admin
-- Email: admin@kayart.com
-- Password: Admin123! (à changer en production)
INSERT INTO `user` (`username`, `email`, `password_hash`, `role`) VALUES
('admin', 'admin@kayart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insertion des catégories
INSERT INTO `category` (`name`, `slug`, `description`) VALUES
('Pagaies', 'pagaies', 'Pagaies artisanales en carbone, bois et fibre de verre'),
('Sièges', 'sieges', 'Sièges de kayak ergonomiques et confortables'),
('Cales', 'cales', 'Cales pieds et genoux pour un meilleur contrôle'),
('Accessoires', 'accessoires', 'Accessoires et équipements pour kayak');

-- Insertion de produits de démonstration
INSERT INTO `product` (`title`, `slug`, `description`, `price`, `weight`, `dimensions`, `image`, `category_id`, `stock`, `sku`, `condition_state`) VALUES
('Pagaie Carbone Compétition 210 cm', 'pagaie-carbone-competition-210', 'Pagaie haut de gamme en carbone intégral avec finition mate, idéale pour la compétition et les longues distances.', 299.99, 0.65, '210cm', 'images/kayart_image1.webp', 1, 10, 'PAG-CARB-COMP-210', 'new'),
('Pagaie Carbone Loisir 215 cm', 'pagaie-carbone-loisir-215', 'Pagaie en carbone légère et maniable, parfaite pour le loisir et les randonnées.', 249.99, 0.70, '215cm', 'images/kayart_image2.webp', 1, 15, 'PAG-CARB-LOIS-215', 'new'),
('Pagaie Carbone Rivière 200 cm', 'pagaie-carbone-riviere-200', 'Pagaie renforcée spécialement conçue pour la rivière et les eaux vives.', 279.99, 0.75, '200cm', 'images/kayart_image3.webp', 1, 12, 'PAG-CARB-RIV-200', 'new'),
('Paire de Pagaies Personnalisées', 'paire-pagaies-personnalisees', 'Paire de pagaies en carbone avec finition bleu métallique personnalisable.', 200.00, 1.40, '210cm (paire)', 'images/kayart_image2.webp', 1, 1, 'PAG-PAIR-PERS', 'new'),
('Siège Kayak Ergonomique', 'siege-kayak-ergonomique', 'Siège ergonomique avec mousse haute densité pour un confort optimal.', 149.99, 1.20, '45cm x 35cm', 'images/default-siege.webp', 2, 8, 'SIEG-ERGO-001', 'new');

-- Insertion de services de démonstration
INSERT INTO `service` (`title`, `description`, `price`, `image`) VALUES
('Réparation de pagaie', 'Réparation professionnelle de vos pagaies endommagées (fibre de verre, carbone).', 50.00, 'images/service-reparation.png'),
('Personnalisation', 'Personnalisation de vos équipements : couleurs, motifs, gravure.', 75.00, 'images/service-personnalisation.png'),
('Conseil et essai', 'Session de conseil personnalisée avec essai de différents modèles.', 0.00, 'images/service-conseil.png');

-- ============================================================
-- VUES UTILES (optionnel)
-- ============================================================

-- Vue pour les produits avec leur catégorie
CREATE OR REPLACE VIEW `v_products_with_category` AS
SELECT 
  p.id,
  p.title,
  p.slug,
  p.description,
  p.price,
  p.discounted_price,
  p.weight,
  p.dimensions,
  p.image,
  p.stock,
  p.sku,
  p.condition_state,
  p.created_at,
  p.updated_at,
  c.id AS category_id,
  c.name AS category_name,
  c.slug AS category_slug
FROM `product` p
LEFT JOIN `category` c ON p.category_id = c.id;

-- Vue pour les réservations avec détails produit
CREATE OR REPLACE VIEW `v_reservations_with_product` AS
SELECT 
  r.id,
  r.customer_name,
  r.customer_email,
  r.customer_phone,
  r.message,
  r.quantity,
  r.status,
  r.admin_notes,
  r.contacted_at,
  r.created_at,
  r.updated_at,
  p.id AS product_id,
  p.title AS product_title,
  p.slug AS product_slug,
  p.price AS product_price,
  p.image AS product_image,
  c.name AS category_name
FROM `reservation` r
INNER JOIN `product` p ON r.product_id = p.id
LEFT JOIN `category` c ON p.category_id = c.id;

-- ============================================================
-- INDEX SUPPLÉMENTAIRES POUR PERFORMANCES
-- ============================================================

-- Index pour améliorer les recherches fréquentes
ALTER TABLE `product` ADD FULLTEXT INDEX `ft_product_search` (`title`, `description`);
ALTER TABLE `service` ADD FULLTEXT INDEX `ft_service_search` (`title`, `description`);

-- ============================================================
-- FIN DU SCRIPT
-- ============================================================
