# Système de Blog / Actualités

## Structure

### Tables de base de données
- `blog_posts` : Articles du blog
- `blog_comments` : Commentaires (avec modération)

### URLs
- **Public** :
  - `/actualites` : Liste des articles
  - `/actualites/{slug}` : Détail d'un article
  
- **Admin** :
  - `/admin/blog` : Gestion des articles
  - `/admin/blog/nouveau` : Créer un article
  - `/admin/blog/edit/{id}` : Modifier un article
  - `/admin/blog/commentaires` : Modération des commentaires

### Fonctionnalités

#### Admin
- ✅ CRUD complet des articles
- ✅ Éditeur riche TinyMCE
- ✅ Upload et redimensionnement d'images
- ✅ Gestion brouillon/publié
- ✅ Slug auto-généré (nettoyage accents)
- ✅ Modération des commentaires
- ✅ Badge de notification pour commentaires en attente

#### Front
- ✅ Grille responsive d'articles
- ✅ Pagination
- ✅ Affichage commentaires approuvés
- ✅ Formulaire de commentaire
- ✅ Messages de validation après envoi

### Sécurité
- ✅ Validation des données (Models)
- ✅ Protection XSS (escaping)
- ✅ Modération obligatoire des commentaires
- ✅ Validation AJAX
- ✅ Protection CSRF (CodeIgniter)

### SEO
- ✅ Slugs propres (sans accents)
- ✅ URLs sémantiques
- ✅ Meta descriptions
- ✅ Contenu frais pour Google

### Images
- Dossier : `writable/uploads/blog/`
- Format original : `{nom}.jpg`
- Format web : `thumb_{nom}.jpg` (800x600px)

### Améliorations futures possibles
- Catégories d'articles
- Tags
- Recherche dans les articles
- Partage social
- Flux RSS
- Système de notification email pour nouveaux commentaires
