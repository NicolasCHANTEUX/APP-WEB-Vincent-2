# Système de Vente : Produits Neufs vs Occasion

## Vue d'ensemble

Le site KAYART implémente deux modes de vente distincts selon l'état du produit :

### 🆕 Produits NEUFS (`condition_state = 'new'`)
- **Mode de vente** : Paiement par carte bancaire (à venir)
- **Affichage** : Interface de paiement en ligne (en développement)
- **Fonctionnalités** :
  - Prix standard sans réduction
  - Quantité sélectionnable
  - Paiement sécurisé en ligne
  - Stock géré automatiquement

### 🔧 Produits OCCASION (`condition_state = 'used'`)
- **Mode de vente** : Formulaire de réservation avec contact humain
- **Raison** : Produit avec petit défaut de conception
- **Affichage** : Formulaire de réservation
- **Fonctionnalités** :
  - Réduction affichée en **pourcentage** (`discount_percent`)
  - Quantité fixe à **1** (pièce unique)
  - Contact personnalisé avant validation
  - Prix barré avec nouveau prix calculé

---

## Structure de la Base de Données

### Champ `discount_percent`
```sql
`discount_percent` DECIMAL(5, 2) DEFAULT NULL 
COMMENT 'Pourcentage de réduction (ex: 15.50 pour 15.50%)'
```

**Exemples de valeurs** :
- `15.00` = 15% de réduction
- `25.50` = 25.5% de réduction
- `NULL` = Pas de réduction

**Calcul du prix final** :
```php
$finalPrice = $price - ($price * ($discountPercent / 100));
```

### Champ `condition_state`
```sql
`condition_state` ENUM('new', 'used') DEFAULT 'new' 
COMMENT 'new=paiement carte, used=réservation avec réduction'
```

---

## Logique d'Affichage

### Page Produit (`product_detail_content.php`)

```php
<?php if ($conditionState === 'used'): ?>
    <!-- PRODUIT OCCASION -->
    - Formulaire de réservation
    - Champ quantité masqué (toujours 1)
    - Badge "Occasion" affiché
    - Prix avec réduction en %
    
<?php else: ?>
    <!-- PRODUIT NEUF -->
    - Interface paiement carte
    - Message "Bientôt disponible"
    - Badge "Neuf" affiché
    - Prix standard
    
<?php endif; ?>
```

### Affichage du Prix avec Réduction

**HTML généré pour produit occasion avec 20% de réduction** :
```html
<div class="flex items-center gap-3">
    <span class="line-through text-gray-500">299.99 €</span>
    <span class="text-red-600">239.99 €</span>
    <span class="badge bg-red-100 text-red-800">-20%</span>
</div>
```

---

## Migration de la Base de Données

### Script à exécuter
```bash
php spark db:query --file=app/Database/scripts/migration_discount_percent.sql
```

Ou manuellement :
```bash
mysql -u root -p boutique_en_ligne < app/Database/scripts/migration_discount_percent.sql
```

### Étapes de la migration
1. ✅ Ajoute `discount_percent` (DECIMAL 5,2)
2. ✅ Convertit `discounted_price` → `discount_percent` (en %)
3. ✅ Supprime `discounted_price`
4. ✅ Met à jour le commentaire de `condition_state`
5. ✅ Recrée la vue `v_products_with_category`

---

## Exemples d'Utilisation

### Créer un produit OCCASION avec réduction

```sql
INSERT INTO `product` (
    `title`, 
    `slug`, 
    `description`, 
    `price`, 
    `discount_percent`,
    `condition_state`,
    `stock`,
    `sku`,
    `category_id`
) VALUES (
    'Pagaie Carbone avec défaut esthétique',
    'pagaie-carbone-defaut-esthetique',
    'Pagaie en carbone avec une petite rayure esthétique, n\'affecte pas la performance.',
    299.99,
    25.00,  -- 25% de réduction
    'used',
    1,      -- Stock de 1 (pièce unique)
    'PAG-CARB-DEFAUT-001',
    1
);
```

**Résultat visible sur le site** :
- Prix affiché : ~~299.99 €~~ **224.99 €** (-25%)
- Badge : "Occasion"
- Formulaire de réservation (pas de paiement en ligne)
- Quantité fixe : 1

### Créer un produit NEUF standard

```sql
INSERT INTO `product` (
    `title`, 
    `slug`, 
    `description`, 
    `price`, 
    `condition_state`,
    `stock`,
    `sku`,
    `category_id`
) VALUES (
    'Pagaie Carbone Premium',
    'pagaie-carbone-premium',
    'Pagaie en carbone haute qualité, parfaite pour la compétition.',
    349.99,
    'new',
    10,
    'PAG-CARB-PREM-001',
    1
);
```

**Résultat visible sur le site** :
- Prix affiché : **349.99 €**
- Badge : "Neuf"
- Interface paiement carte (à venir)
- Quantité sélectionnable (1-10)

---

## Traductions

### Français (`app/Language/fr/Texts.php`)
```php
'payment_card_title'          => 'Paiement par carte',
'payment_card_coming_soon'    => 'Le paiement en ligne sera bientôt disponible pour ce produit.',
'payment_card_methods'        => 'Visa, Mastercard et autres cartes acceptées',
'payment_temporary_alternative' => 'En attendant la mise en place du paiement en ligne',
'payment_contact_us'          => 'Contactez-nous pour finaliser votre commande',
```

### Anglais (`app/Language/en/Texts.php`)
```php
'payment_card_title'          => 'Card Payment',
'payment_card_coming_soon'    => 'Online payment will be available soon for this product.',
'payment_card_methods'        => 'Visa, Mastercard and other cards accepted',
'payment_temporary_alternative' => 'While online payment is being set up',
'payment_contact_us'          => 'Contact us to finalize your order',
```

---

## TODO : Paiement en Ligne

### Étapes futures pour produits NEUFS
1. Intégrer Stripe ou PayPal
2. Créer table `order` et `order_item`
3. Implémenter panier d'achat
4. Gestion des stocks automatique
5. Emails de confirmation
6. Factures PDF

### Pour l'instant (produits NEUFS)
- Message "Bientôt disponible"
- Redirection vers page contact
- Gestion manuelle des commandes

---

## Résumé Visuel

| Critère | NEUF (`new`) | OCCASION (`used`) |
|---------|--------------|-------------------|
| **Achat** | Paiement carte (à venir) | Réservation + contact |
| **Prix** | Standard | Réduit (%) |
| **Quantité** | 1 à stock | Fixe à 1 |
| **Badge** | "Neuf" (bleu) | "Occasion" (orange) |
| **Réduction** | Non | Oui (en %) |
| **Stock** | Multiple | Unique |
| **Formulaire** | Paiement | Réservation |

---

## Fichiers Modifiés

1. ✅ `app/Database/scripts/init_database.sql`
2. ✅ `app/Database/scripts/migration_discount_percent.sql` (nouveau)
3. ✅ `app/Views/components/section/produits/product_detail_content.php`
4. ✅ `app/Language/fr/Texts.php`
5. ✅ `app/Language/en/Texts.php`

---

**Date de mise en place** : 31 décembre 2025  
**Développeur** : GitHub Copilot
