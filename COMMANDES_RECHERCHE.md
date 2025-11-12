# 🚀 Commandes à exécuter - Recherche et filtres

## ✅ Étapes finales

### 1️⃣ Vider le cache Symfony
```bash
php bin/console cache:clear
```

### 2️⃣ Compiler les assets CSS/JS
```bash
npm run build
```

Ou en mode watch pour le développement :
```bash
npm run watch
```

### 3️⃣ Tester la recherche
Allez sur : `http://localhost:8000/game`

---

## 🧪 Tests à effectuer

### ✅ Test 1 : Recherche simple
1. Tapez "Mario" dans la barre de recherche
2. Cliquez sur "Rechercher"
3. Vérifiez que les résultats contiennent "Mario" dans le titre, série, éditeur ou développeur

### ✅ Test 2 : Filtres avancés
1. Cliquez sur "⚙️ Filtres avancés"
2. Sélectionnez une console
3. Cliquez sur "Appliquer"
4. Vérifiez que seuls les jeux de cette console s'affichent

### ✅ Test 3 : Recherche alphabétique
1. Cliquez sur la lettre "M"
2. Vérifiez que seuls les jeux commençant par M s'affichent
3. Cliquez sur "Tous" pour réinitialiser

### ✅ Test 4 : Combinaison de filtres
1. Recherche : "Final"
2. Console : "PlayStation 5"
3. Année : "2024"
4. Cliquez sur "Appliquer"
5. Vérifiez que les résultats respectent tous les critères

### ✅ Test 5 : Retirer un filtre
1. Appliquez plusieurs filtres
2. Cliquez sur le "✕" d'un tag de filtre actif
3. Vérifiez que ce filtre est retiré et les résultats mis à jour

### ✅ Test 6 : Compteur de résultats
1. Appliquez des filtres
2. Vérifiez que le compteur affiche le bon nombre de résultats
3. Ex: "15 jeux trouvés"

### ✅ Test 7 : Responsive
1. Testez sur mobile (réduisez la fenêtre)
2. Vérifiez que les filtres passent en 1 colonne
3. Vérifiez que l'alphabet passe sur plusieurs lignes

---

## ⚠️ Si vous rencontrez des problèmes

### Problème : Les filtres ne s'affichent pas
**Solution :**
```bash
npm run build
php bin/console cache:clear
```

### Problème : Erreur SQL avec REGEXP
**Solution :** REGEXP fonctionne avec MySQL/MariaDB. Si vous utilisez PostgreSQL, modifiez la méthode dans GameRepository.php :
```php
// Au lieu de :
$qb->andWhere('g.title REGEXP :regex')

// Utilisez :
$qb->andWhere('g.title ~ :regex') // PostgreSQL
```

### Problème : Les années ne s'affichent pas
**Vérification :**
1. Assurez-vous que vos jeux ont une date de sortie (`releaseDate`)
2. Vérifiez dans la base de données que la colonne n'est pas NULL

### Problème : Erreur "findBySearchAndFilters not found"
**Solution :**
```bash
php bin/console cache:clear --env=prod
php bin/console cache:clear --env=dev
```

---

## 🎨 Personnalisation

### Changer les couleurs des filtres
Modifiez dans `assets/styles/retro-theme.scss` :

```scss
.alphabet-btn.active {
    background: $color-accent; // Au lieu de $color-primary
    border-color: $color-accent;
}
```

### Ajouter d'autres champs à la recherche textuelle
Dans `src/Repository/GameRepository.php`, ajoutez dans le `orX()` :

```php
$qb->expr()->like('g.nouveauChamp', ':search')
```

### Modifier le nombre de colonnes des filtres
Dans `assets/styles/retro-theme.scss` :

```scss
.filters-grid {
    @media (min-width: $breakpoint-desktop) {
        grid-template-columns: repeat(5, 1fr); // Au lieu de 4
    }
}
```

---

## 📊 Statistiques de recherche

Pour voir les performances, vous pouvez ajouter un log dans GameRepository.php :

```php
public function findBySearchAndFilters(array $criteria = []): array
{
    $startTime = microtime(true);
    
    // ... votre code existant ...
    
    $results = $qb->getQuery()->getResult();
    
    $duration = microtime(true) - $startTime;
    error_log("Search took: " . $duration . "s for " . count($results) . " results");
    
    return $results;
}
```

---

## 🔧 Maintenance

### Ajouter un nouveau filtre

1. **Backend** - Dans GameRepository.php :
```php
if (!empty($criteria['nouveauFiltre'])) {
    $qb->andWhere('g.nouveauChamp = :nouveauFiltre')
       ->setParameter('nouveauFiltre', $criteria['nouveauFiltre']);
}
```

2. **Controller** - Dans GameController.php :
```php
'nouveauFiltre' => $request->query->get('nouveauFiltre'),
```

3. **Template** - Dans index.html.twig :
```html
<div class="filter-group">
    <label for="nouveauFiltre" class="filter-label">Nouveau filtre</label>
    <select name="nouveauFiltre" id="nouveauFiltre" class="filter-select">
        <option value="">Tous</option>
        <!-- Options -->
    </select>
</div>
```

---

## ✅ Checklist de vérification

- [ ] Cache Symfony vidé
- [ ] Assets compilés (npm run build)
- [ ] Recherche simple fonctionne
- [ ] Filtres avancés s'ouvrent/ferment
- [ ] Console filtre fonctionne
- [ ] Année filtre fonctionne
- [ ] Genre filtre fonctionne
- [ ] Mode filtre fonctionne
- [ ] Alphabet fonctionne
- [ ] Tags de filtres actifs s'affichent
- [ ] Bouton "✕" retire les filtres
- [ ] Compteur de résultats est correct
- [ ] Responsive fonctionne sur mobile
- [ ] Aucune erreur dans la console du navigateur

---

Tout est prêt pour la recherche ! 🎉

