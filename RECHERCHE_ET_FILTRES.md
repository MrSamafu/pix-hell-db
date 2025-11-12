# ✅ Barre de recherche et filtres avancés - Jeux vidéo

## 🔍 Fonctionnalités implémentées

### Barre de recherche principale
- ✅ **Recherche textuelle** dans :
  - Titre du jeu
  - Série (ex: Mario, Sonic)
  - Éditeur
  - Développeur
- ✅ **Icône de recherche** 🔍
- ✅ **Bouton "✕" pour effacer** la recherche rapidement
- ✅ **Placeholder** explicatif

### Filtres avancés (menu déroulant)
- ✅ **Console/Plateforme** 🕹️ - Liste déroulante des consoles
- ✅ **Année de sortie** 📅 - Liste des années disponibles
- ✅ **Genre** 🎯 - Liste des genres (Action, RPG, etc.)
- ✅ **Mode de jeu** 🎮 - Liste des modes (Solo, Multijoueur, etc.)
- ✅ **Recherche alphabétique** 🔤 - Lettres A-Z + 0-9

### Interface utilisateur
- ✅ **Menu déroulant** pour les filtres avancés (click pour ouvrir/fermer)
- ✅ **Ouverture automatique** si des filtres sont actifs
- ✅ **Tags de filtres actifs** avec possibilité de les retirer individuellement
- ✅ **Compteur de résultats** ("X jeux trouvés")
- ✅ **Boutons d'action** : "Appliquer" et "Réinitialiser"
- ✅ **Design responsive** (mobile, tablette, desktop)

---

## 📁 Fichiers modifiés

### 1. Backend - Repository
**src/Repository/GameRepository.php**
- ✅ Nouvelle méthode `findBySearchAndFilters(array $criteria)` - Recherche multi-critères
- ✅ Nouvelle méthode `findAvailableYears()` - Récupère les années disponibles
- ✅ Support des jointures pour genres et modes
- ✅ Recherche LIKE pour titre, série, éditeur, développeur
- ✅ Filtre par année avec `YEAR()`
- ✅ Recherche alphabétique avec REGEXP pour 0-9

### 2. Backend - Controller
**src/Controller/GameController.php**
- ✅ Ajout des imports : `ConsoleRepository`, `GenreRepository`, `ModeRepository`
- ✅ Méthode `index()` mise à jour avec paramètres de recherche
- ✅ Récupération des critères depuis l'URL (`$request->query->get()`)
- ✅ Passage des données aux templates (consoles, genres, modes, années, alphabet)

### 3. Frontend - Template
**templates/game/index.html.twig**
- ✅ Formulaire de recherche avec GET method
- ✅ Barre de recherche avec icône et bouton clear
- ✅ Section filtres avancés collapsible
- ✅ Grille de 4 colonnes pour les filtres (responsive)
- ✅ Alphabet en grille de boutons cliquables
- ✅ Tags de filtres actifs avec bouton "✕"
- ✅ Compteur de résultats
- ✅ JavaScript pour toggle des filtres

### 4. Frontend - Styles
**assets/styles/retro-theme.scss**
- ✅ `.search-container`, `.search-bar`, `.search-input-wrapper`
- ✅ `.search-icon`, `.search-input`, `.search-clear`
- ✅ `.search-filters`, `.filters-toggle`, `.filters-content`
- ✅ `.filters-grid` - Grille responsive (1 → 2 → 4 colonnes)
- ✅ `.filter-group`, `.filter-label`, `.filter-select`
- ✅ `.alphabet-grid`, `.alphabet-btn` avec état active
- ✅ `.active-filters`, `.filter-tag`, `.filter-tag-remove`
- ✅ `.search-results-count`

---

## 🎨 Design & UX

### Layout
```
┌────────────────────────────────────────────────────────┐
│ 🔍 [Rechercher un jeu...]                [Rechercher] │
├────────────────────────────────────────────────────────┤
│ ⚙️ Filtres avancés ▼                                   │
│ ┌────────────────────────────────────────────────────┐ │
│ │ 🕹️ Console   📅 Année   🎯 Genre   🎮 Mode        │ │
│ │ [Select ▼]   [Select ▼] [Select ▼] [Select ▼]     │ │
│ │                                                     │ │
│ │ 🔤 Recherche alphabétique                          │ │
│ │ [Tous] [0-9] [A] [B] [C] ... [Z]                  │ │
│ │                                                     │ │
│ │ [✓ Appliquer] [✕ Réinitialiser]                   │ │
│ └────────────────────────────────────────────────────┘ │
├────────────────────────────────────────────────────────┤
│ Filtres actifs: [🔍 "Mario" ✕] [📅 2023 ✕]           │
├────────────────────────────────────────────────────────┤
│ 15 jeux trouvés                                        │
└────────────────────────────────────────────────────────┘
```

### Interactions
1. **Recherche simple** : Taper dans la barre → Cliquer "Rechercher" ou Enter
2. **Filtres avancés** : Cliquer sur "⚙️ Filtres avancés" pour ouvrir/fermer
3. **Recherche alphabétique** : Cliquer sur une lettre (navigation immédiate)
4. **Retirer un filtre** : Cliquer sur "✕" dans les tags de filtres actifs
5. **Réinitialiser** : Bouton "Réinitialiser" ou cliquer "Tous" dans l'alphabet

### Responsive
- **Mobile** : 1 colonne pour les filtres, alphabet en plusieurs lignes
- **Tablette** : 2 colonnes pour les filtres
- **Desktop** : 4 colonnes pour les filtres

---

## 🔧 Fonctionnement technique

### URL avec paramètres
```
/game?search=mario&platform=5&year=2023&genre=2&mode=1&letter=M
```

### Critères de recherche
| Paramètre | Type | Description |
|-----------|------|-------------|
| `search` | string | Recherche textuelle (titre, série, éditeur, développeur) |
| `platform` | int | ID de la console |
| `year` | int | Année de sortie |
| `genre` | int | ID du genre |
| `mode` | int | ID du mode de jeu |
| `letter` | string | Première lettre (A-Z ou "0-9") |

### Méthode de recherche
```php
public function findBySearchAndFilters(array $criteria = []): array
{
    // Jointures : platform, genres, modes
    // Conditions WHERE dynamiques
    // LIKE pour recherche textuelle
    // YEAR() pour filtre année
    // REGEXP pour recherche alphabétique (0-9)
    // ORDER BY title ASC
}
```

---

## 🚀 Pour tester

### 1️⃣ Compiler les assets
```bash
npm run build
```

### 2️⃣ Aller sur la page des jeux
```
/game
```

### 3️⃣ Tester les fonctionnalités

**Recherche simple :**
- Taper "Mario" dans la barre de recherche
- Voir les résultats filtrés

**Filtres avancés :**
- Cliquer sur "⚙️ Filtres avancés"
- Sélectionner une console
- Sélectionner une année
- Cliquer "Appliquer"

**Recherche alphabétique :**
- Cliquer sur la lettre "M"
- Voir uniquement les jeux commençant par M
- Cliquer sur "0-9" pour les jeux commençant par un chiffre

**Combiner plusieurs filtres :**
- Recherche : "Mario"
- Console : "Nintendo Switch"
- Année : "2023"
- Genre : "Plateforme"
- Voir les tags de filtres actifs

**Retirer un filtre :**
- Cliquer sur "✕" dans un tag de filtre actif
- Le filtre est retiré, les résultats se mettent à jour

---

## ✨ Points forts

- ✅ **Interface intuitive** avec icônes et labels clairs
- ✅ **Recherche puissante** dans 4 champs différents
- ✅ **Filtres combinables** pour une recherche précise
- ✅ **Navigation rapide** avec l'alphabet
- ✅ **Feedback visuel** avec compteur et tags actifs
- ✅ **Responsive** sur tous les écrans
- ✅ **Performance** avec requête optimisée (jointures)
- ✅ **UX améliorée** avec ouverture auto des filtres si actifs

---

## 📊 Exemples d'utilisation

### Cas 1 : Trouver un jeu Mario sur Switch
1. Taper "Mario" dans la recherche
2. Sélectionner "Nintendo Switch" dans Console
3. Cliquer "Rechercher"

### Cas 2 : Tous les RPG de 2023
1. Ouvrir les filtres avancés
2. Sélectionner "2023" dans Année
3. Sélectionner "RPG" dans Genre
4. Cliquer "Appliquer"

### Cas 3 : Jeux commençant par "Z"
1. Cliquer sur la lettre "Z" dans l'alphabet
2. Voir tous les jeux commençant par Z

### Cas 4 : Recherche complexe
- Recherche : "Final"
- Console : "PlayStation 5"
- Année : "2024"
- Genre : "Action-RPG"
- Mode : "Solo"
→ Trouve les jeux Final Fantasy sur PS5, sortis en 2024, Action-RPG, solo

---

Tout est prêt ! La recherche est puissante et intuitive. 🎉

