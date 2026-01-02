# 🎯 Distinction : Demandes vs Réservations

## ✅ Configuration actuelle (complétée)

### 📧 Admin > Demandes (`/admin/demandes`)
**Objectif** : Gérer les demandes de contact générales envoyées via le formulaire de contact du site.

#### Base de données
- **Table** : `contact_request`
- **Champs** : `name`, `email`, `subject`, `message`, `status`, `admin_reply`, `replied_at`

#### Statuts
- `new` → Nouvelle demande (non lue)
- `in_progress` → En cours de traitement
- `completed` → Traitée / Réponse envoyée
- `archived` → Archivée

#### Modèle & Contrôleur
- **Modèle** : `ContactRequestModel`
- **Contrôleur** : `AdminDemandesController`
- **Méthodes** :
  - `index()` - Liste toutes les demandes
  - `show($id)` - Affiche le détail
  - `updateStatus($id)` - Met à jour le statut et enregistre la réponse admin

#### Interface
**Liste** : Affiche Client | Sujet | Message (extrait) | Statut | Date | Actions
**Détail** : Sujet + Message complet + Infos client + Formulaire de traitement (statut + réponse)

---

## 🛒 Admin > Réservations (à implémenter plus tard)

### Objectif
Gérer les demandes de réservation/achat liées à un **produit spécifique**.

#### Base de données
- **Table** : `reservation`
- **Champs** : `product_id`, `customer_name`, `customer_email`, `customer_phone`, `message`, `quantity`, `status`, `admin_notes`, `contacted_at`

#### Statuts prévus
- `new` → Nouvelle réservation
- `contacted` → Client contacté
- `confirmed` → Réservation confirmée
- `completed` → Vente finalisée
- `cancelled` → Annulée

#### Modèle & Contrôleur (à créer)
- **Modèle** : `ReservationModel` (existe déjà)
- **Contrôleur** : `AdminReservationsController` (à créer - distinct de AdminDemandesController)
- **Routes** : `/admin/reservations` (à ajouter)

#### Interface (future)
**Liste** : Client | Produit | Quantité | Prix total | Statut | Date | Actions
**Détail** : Infos produit (image, titre, prix, stock) + Infos client + Message + Formulaire traitement

---

## 🔑 Différences clés

| Aspect | Demandes (contact) | Réservations (produit) |
|--------|-------------------|----------------------|
| **Table DB** | `contact_request` | `reservation` |
| **Lien produit** | ❌ Non | ✅ Oui (`product_id`) |
| **URL admin** | `/admin/demandes` | `/admin/reservations` |
| **Modèle** | `ContactRequestModel` | `ReservationModel` |
| **Contrôleur** | `AdminDemandesController` | `AdminReservationsController` |
| **Quantité** | ❌ Non | ✅ Oui |
| **Prix** | ❌ Non | ✅ Oui (calculé) |
| **Réponse admin** | ✅ Oui (`admin_reply`) | ⚠️ Non (utilise `admin_notes`) |

---

## 📝 Résumé

### ✅ Fait
- Section "Demandes" fonctionnelle
- Affiche les `contact_request`
- Formulaire de contact général
- Système de réponse admin intégré

### ⏳ À faire (plus tard)
- Section "Réservations" 
- Affichera les `reservation`
- Bouton "Réserver" sur fiches produits
- Gestion stock et commandes

---

## 🚀 Prochaines étapes recommandées

1. **Tester la section Demandes**
   - Créer une demande test via le formulaire de contact
   - Vérifier l'affichage dans `/admin/demandes`
   - Tester la mise à jour du statut
   - Tester l'ajout d'une réponse admin

2. **Plus tard : Implémenter les Réservations**
   - Créer le contrôleur dédié
   - Créer les vues spécifiques
   - Ajouter les routes
   - Intégrer avec les fiches produits

---

✨ **Important** : Les deux systèmes sont **indépendants** et gèrent des cas d'usage différents !
