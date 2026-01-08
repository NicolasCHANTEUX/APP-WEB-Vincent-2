# 🔄 Migration des Sessions : FileHandler → DatabaseHandler

## 📋 Contexte

Le stockage des sessions dans des **fichiers** (`writable/session`) pose des problèmes de permissions récurrents sur le serveur Linux, surtout avec Apache (`www-data`) et les déploiements SSH.

**Solution définitive** : Stocker les sessions dans **MySQL** via `DatabaseHandler`.

---

## ✅ Ce qui a été fait en local

1. ✅ **Migration créée** : `2026-01-09-000000_CreateSessionTable.php`
2. ✅ **Configuration modifiée** : `app/Config/Session.php`
   - Driver changé de `FileHandler` → `DatabaseHandler`
   - SavePath changé de `WRITEPATH . 'session'` → `'ci_sessions'`
3. ✅ **Table créée** : `ci_sessions` avec les champs :
   - `id` (VARCHAR 128, PRIMARY KEY)
   - `ip_address` (VARCHAR 45)
   - `timestamp` (TIMESTAMP)
   - `data` (BLOB)

---

## 🚀 Procédure de déploiement sur le serveur

### Étape 1 : Se connecter en SSH

```bash
ssh nicolas@tonserveur.fr
cd /var/www/kayart
```

### Étape 2 : Pull des modifications Git

```bash
git pull origin main
```

### Étape 3 : Exécuter la migration

```bash
php spark migrate
```

**Résultat attendu** :
```
Running all new migrations...
Migrations complete.
```

### Étape 4 : Vérifier que la table existe

```bash
php spark db:table ci_sessions
```

Tu devrais voir la structure de la table avec les 4 colonnes.

### Étape 5 : Vider le cache (si nécessaire)

```bash
php spark cache:clear
```

### Étape 6 : Redémarrer PHP-FPM

```bash
sudo systemctl reload php8.3-fpm
```

---

## 🎯 Résultats

### ✅ Avantages

- **Fini les problèmes de permissions** : Plus besoin de `chmod 777` ou `chown www-data`
- **Plus robuste** : Les sessions survivent aux redémarrages du serveur web
- **Meilleure scalabilité** : Facilite la réplication sur plusieurs serveurs
- **Nettoyage automatique** : CodeIgniter gère le garbage collection

### ⚠️ Points d'attention

- **Les utilisateurs seront déconnectés une seule fois** (lors du passage à DatabaseHandler)
- Les anciennes sessions dans `writable/session/` seront ignorées (tu peux les supprimer)

---

## 🧹 Nettoyage optionnel (après vérification)

Une fois que tu confirmes que les sessions fonctionnent en base de données :

```bash
# Supprimer les anciens fichiers de session
rm -rf writable/session/ci_session*

# (Garde le dossier avec juste un .gitkeep si besoin)
```

---

## 🐛 Dépannage

### Problème : "Table 'ci_sessions' doesn't exist"

```bash
# Re-exécuter la migration
php spark migrate:refresh
```

### Problème : "SQLSTATE[42000]: Access denied"

Vérifie les credentials MySQL dans `app/Config/Database.php` (ou `.env`).

---

## 📝 Fichiers modifiés

- `app/Config/Session.php` : Driver et savePath
- `app/Database/Migrations/2026-01-09-000000_CreateSessionTable.php` : Migration SQL

---

**Date de création** : 9 janvier 2026  
**Impact** : Zéro downtime (les utilisateurs seront juste déconnectés une fois)
