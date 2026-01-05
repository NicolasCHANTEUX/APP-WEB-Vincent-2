# 🔧 Tests de la fonctionnalité Demandes de Contact

## ✅ Modifications effectuées

### 1. ContactControler.php
- ✅ Ajout de `ContactRequestModel`
- ✅ Sauvegarde en base de données au lieu de juste afficher un message
- ✅ Gestion des erreurs d'insertion

### 2. Structure de test

## 📝 Test manuel

### Étape 1 : Vérifier que la table existe
```bash
php spark db:table contact_request
```
✅ Table existe et est vide

### Étape 2 : Envoyer un message de test
1. Aller sur `/contact`
2. Remplir le formulaire :
   - Nom : "Test Client"
   - Email : "test@example.com"
   - Sujet : "Demande de renseignement"
   - Message : "Ceci est un message de test pour vérifier que l'enregistrement fonctionne."
3. Cliquer sur "Envoyer"

### Étape 3 : Vérifier dans la base de données
```bash
php spark db:table contact_request
```
Devrait afficher la nouvelle demande

### Étape 4 : Vérifier dans l'interface admin
1. Se connecter à `/admin`
2. Aller dans "Demandes" (`/admin/demandes`)
3. La demande devrait apparaître avec le statut "Nouvelles"

### Étape 5 : Tester le détail
1. Cliquer sur l'icône "œil" pour voir le détail
2. Vérifier que toutes les informations sont affichées
3. Tester la mise à jour du statut

---

## 🐛 Débogage

Si la demande n'apparaît pas :

1. **Vérifier les logs**
   ```
   writable/logs/log-YYYY-MM-DD.log
   ```

2. **Vérifier la validation**
   - Les champs sont-ils tous requis ?
   - Les règles de validation correspondent-elles au formulaire ?

3. **Vérifier l'insertion**
   Ajouter temporairement dans `ContactControler::sendEmail()` :
   ```php
   log_message('debug', 'Tentative d\'insertion: ' . json_encode($data));
   $result = $this->contactRequestModel->insert($data);
   log_message('debug', 'Résultat insertion: ' . ($result ? 'SUCCESS' : 'FAILED'));
   if (!$result) {
       log_message('error', 'Erreurs: ' . json_encode($this->contactRequestModel->errors()));
   }
   ```

---

## ✅ Checklist de vérification

- [ ] Table `contact_request` existe
- [ ] Formulaire de contact affiche bien les champs
- [ ] Validation fonctionne (tester avec des données invalides)
- [ ] Message de succès s'affiche après envoi
- [ ] Demande apparaît dans `/admin/demandes`
- [ ] Détail de la demande est accessible
- [ ] Mise à jour du statut fonctionne
- [ ] Compteur "Nouvelles demandes" sur le dashboard est correct

---

## 🎯 Prochaines améliorations

- [ ] Ajouter notification email à l'admin lors d'une nouvelle demande
- [ ] Ajouter email de confirmation au client
- [ ] Gérer les pièces jointes (images)
- [ ] Ajouter pagination sur la liste des demandes
- [ ] Ajouter recherche/filtres avancés
