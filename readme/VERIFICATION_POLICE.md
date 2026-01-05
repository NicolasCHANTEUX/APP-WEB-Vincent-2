# 🚀 Guide de vérification - Police locale

## ✅ Vérification rapide (3 étapes)

### 1️⃣ Vérifier que la police est chargée

**Ouvrir DevTools (F12) → Network → Filtrer "font"**

Vous devriez voir :
```
✅ roboto-900-italic.woff2
   Status: 200 OK
   Size: 12.5 KB
   Time: ~10ms (local)
   Type: font/woff2
```

**PAS de requête vers:**
```
❌ fonts.googleapis.com
❌ fonts.gstatic.com
```

---

### 2️⃣ Vérifier les headers de cache

**Cliquer sur `roboto-900-italic.woff2` → Response Headers**

Vous devriez voir :
```http
✅ Cache-Control: public, max-age=31536000, immutable
✅ Expires: [date dans 1 an]
```

---

### 3️⃣ Tester le cache

1. **Recharger la page (F5)**
2. **Re-filtrer "font" dans Network**
3. **Vérifier le status:**

```
✅ Status: 200 (from disk cache)
   ou
✅ Status: 304 Not Modified
   ou
✅ Size: (memory cache)
```

➡️ **Si vous voyez un de ces statuts = cache fonctionne !**

---

## 🧪 Test Lighthouse (score attendu)

### Avant
```
Performance: 96
❌ Eliminate render-blocking resources: 370ms
   - fonts.googleapis.com
   - fonts.gstatic.com
```

### Après
```
Performance: 98-100 🎉
✅ Eliminate render-blocking resources: PASS
   - Aucune requête externe
```

---

## 🎨 Test visuel

Le texte **KAYART** dans le hero doit :
- ✅ S'afficher avec la police Roboto 900 italic
- ✅ Apparaître immédiatement (pas de flash)
- ✅ Avoir le même rendu qu'avant

---

## 🔧 Dépannage

### La police ne charge pas ?

**Vérifier le chemin:**
```html
<!-- Dans root_layout.php -->
<link rel="preload" href="/fonts/roboto-900-italic.woff2" as="font" type="font/woff2" crossorigin>
```

**Vérifier que le fichier existe:**
```powershell
Test-Path "public/fonts/roboto-900-italic.woff2"
# Doit retourner: True
```

### Police différente ?

**Vérifier la déclaration CSS:**
```css
@font-face {
    font-family: 'Roboto';  /* ← Nom exact */
    font-weight: 900;       /* ← Poids exact */
    font-style: italic;     /* ← Style exact */
}
```

**Vérifier l'utilisation:**
```css
/* Dans Tailwind ou CSS custom */
font-family: 'Roboto', sans-serif;
font-weight: 900;
font-style: italic;
```

### Pas de cache ?

**Avec php spark serve (développement):**
- Le filter `CacheHeadersFilter` doit être activé
- Vérifier dans `app/Config/Filters.php` : `'cacheheaders'` dans `$globals['after']`

**En production Apache:**
- Vérifier que `mod_headers` et `mod_expires` sont activés
- Le `.htaccess` dans `public/` contient déjà la config

---

## 📊 Métriques attendues

| Métrique | Cible |
|----------|-------|
| Score Performance | 98-100 |
| FCP | < 1.8s |
| LCP | < 2.5s |
| TBT | < 200ms |
| CLS | < 0.1 |

---

## 🎯 Checklist finale

- [ ] Police visible dans Network (roboto-900-italic.woff2)
- [ ] Pas de requête Google Fonts
- [ ] Headers de cache présents (Cache-Control: immutable)
- [ ] Cache fonctionne (disk cache au 2ème chargement)
- [ ] Texte KAYART s'affiche correctement
- [ ] Score Lighthouse > 95
- [ ] Aucune erreur console

**Si tous les points sont ✅ : C'EST PARFAIT !**
