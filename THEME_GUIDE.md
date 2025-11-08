# Guide d'utilisation du thème PixHellDB

## 🎨 Palette de couleurs

- **Dark** : `#0D0A0B` - Couleur de fond principale
- **Cream** : `#F0F0C9` - Couleur de texte principale
- **Purple** : `#6761A8` - Couleur primaire
- **Red** : `#A30015` - Couleur accent
- **Teal** : `#A0C1B9` - Couleur secondaire

## 📦 Classes disponibles

### Layout & Conteneurs

```html
<div class="container">
    <!-- Conteneur centré avec max-width -->
</div>
```

### Grilles Responsives

```html
<!-- Grille 2 colonnes -->
<div class="grid grid--2">
    <div>Colonne 1</div>
    <div>Colonne 2</div>
</div>

<!-- Grille 3 colonnes -->
<div class="grid grid--3">
    <div>Colonne 1</div>
    <div>Colonne 2</div>
    <div>Colonne 3</div>
</div>

<!-- Grille 4 colonnes -->
<div class="grid grid--4">
    <div>Colonne 1</div>
    <div>Colonne 2</div>
    <div>Colonne 3</div>
    <div>Colonne 4</div>
</div>
```

### Navigation

```html
<nav class="navbar">
    <div class="navbar__container">
        <a class="navbar__brand" href="/">Brand<span>Name</span></a>
        <button class="navbar__toggle" onclick="toggleMenu()">☰</button>
        <ul class="navbar__menu" id="navMenu">
            <li><a class="navbar__link" href="#">Link</a></li>
            <li><a class="navbar__link active" href="#">Active Link</a></li>
        </ul>
    </div>
</nav>
```

### Cards

```html
<div class="card">
    <img src="image.jpg" alt="Image" class="card__image">
    <div class="card__body">
        <h3 class="card__title">Titre</h3>
        <p class="card__text">Description du contenu...</p>
    </div>
    <div class="card__footer">
        <a href="#" class="btn btn--primary">Action</a>
    </div>
</div>
```

### Boutons

```html
<!-- Bouton primaire (violet) -->
<button class="btn btn--primary">Bouton Primaire</button>

<!-- Bouton secondaire (teal) -->
<button class="btn btn--secondary">Bouton Secondaire</button>

<!-- Bouton accent (rouge) -->
<button class="btn btn--accent">Bouton Accent</button>

<!-- Bouton outline -->
<button class="btn btn--outline">Bouton Outline</button>

<!-- Tailles -->
<button class="btn btn--primary btn--small">Petit</button>
<button class="btn btn--primary">Normal</button>
<button class="btn btn--primary btn--large">Grand</button>
```

### Formulaires

```html
<form>
    <!-- Champ de texte -->
    <div class="form__group">
        <label class="form__label">Nom</label>
        <input type="text" class="form__input" placeholder="Votre nom">
    </div>

    <!-- Zone de texte -->
    <div class="form__group">
        <label class="form__label">Message</label>
        <textarea class="form__textarea" placeholder="Votre message"></textarea>
    </div>

    <!-- Select -->
    <div class="form__group">
        <label class="form__label">Choix</label>
        <select class="form__select">
            <option>Option 1</option>
            <option>Option 2</option>
        </select>
    </div>

    <!-- Checkbox -->
    <div class="form__checkbox">
        <input type="checkbox" id="check1">
        <label for="check1">J'accepte les conditions</label>
    </div>

    <!-- Message d'erreur -->
    <p class="form__error">Ce champ est requis</p>
</form>
```

### Alertes

```html
<!-- Succès -->
<div class="alert alert--success">
    Opération réussie !
</div>

<!-- Erreur -->
<div class="alert alert--error">
    Une erreur est survenue
</div>

<!-- Info -->
<div class="alert alert--info">
    Information importante
</div>
```

### Badges

```html
<span class="badge badge--primary">Primaire</span>
<span class="badge badge--secondary">Secondaire</span>
<span class="badge badge--accent">Accent</span>
```

### Tableaux

```html
<table class="table">
    <thead>
        <tr>
            <th>Colonne 1</th>
            <th>Colonne 2</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Données 1</td>
            <td>Données 2</td>
        </tr>
    </tbody>
</table>
```

### Utilitaires de texte

```html
<!-- Alignement -->
<p class="text--center">Centré</p>
<p class="text--left">Gauche</p>
<p class="text--right">Droite</p>

<!-- Couleurs -->
<p class="text--muted">Texte atténué</p>
<p class="text--accent">Texte accent</p>
<p class="text--primary">Texte primaire</p>
```

### Espacements

```html
<!-- Marges top -->
<div class="mt-sm">Marge top petite</div>
<div class="mt-md">Marge top moyenne</div>
<div class="mt-lg">Marge top grande</div>
<div class="mt-xl">Marge top extra large</div>

<!-- Marges bottom -->
<div class="mb-sm">Marge bottom petite</div>
<div class="mb-md">Marge bottom moyenne</div>
<div class="mb-lg">Marge bottom grande</div>
<div class="mb-xl">Marge bottom extra large</div>

<!-- Marges verticales (top + bottom) -->
<div class="my-sm">Marges verticales petites</div>
<div class="my-md">Marges verticales moyennes</div>
<div class="my-lg">Marges verticales grandes</div>

<!-- Paddings verticaux -->
<div class="py-sm">Padding vertical petit</div>
<div class="py-md">Padding vertical moyen</div>
<div class="py-lg">Padding vertical grand</div>
```

### Display

```html
<div class="d-block">Block</div>
<div class="d-inline">Inline</div>
<div class="d-inline-block">Inline Block</div>
<div class="d-flex">Flex</div>
<div class="d-grid">Grid</div>
<div class="d-none">Caché</div>
```

### Flexbox

```html
<div class="d-flex flex--row">Direction row</div>
<div class="d-flex flex--column">Direction column</div>
<div class="d-flex flex--wrap">Wrap</div>
<div class="d-flex flex--center">Centré (horizontal + vertical)</div>
<div class="d-flex flex--between">Space between</div>
<div class="d-flex flex--around">Space around</div>
```

### Gaps (espacement entre éléments flex/grid)

```html
<div class="d-flex gap-sm">Gap petit</div>
<div class="d-flex gap-md">Gap moyen</div>
<div class="d-flex gap-lg">Gap grand</div>
```

### Responsive

```html
<!-- Caché sur mobile -->
<div class="hide-mobile">Visible seulement sur desktop/tablet</div>

<!-- Caché sur desktop -->
<div class="hide-desktop">Visible seulement sur mobile</div>
```

### Animations

```html
<div class="animate-fade-in">Animation fade in</div>
<div class="animate-slide-in">Animation slide in</div>
```

## 📱 Breakpoints

- **Mobile** : < 576px
- **Tablet** : 576px - 1024px
- **Desktop** : > 1024px
- **Wide** : > 1440px

## 🎯 Exemples d'utilisation

### Page avec grille de cards

```twig
{% extends 'base.html.twig' %}

{% block body %}
<div class="container">
    <h1 class="text--center mb-xl">Mes Jeux</h1>
    
    <div class="grid grid--3">
        {% for game in games %}
        <div class="card">
            <img src="{{ game.image }}" class="card__image" alt="{{ game.title }}">
            <div class="card__body">
                <h3 class="card__title">{{ game.title }}</h3>
                <p class="card__text">{{ game.description|slice(0, 100) }}...</p>
            </div>
            <div class="card__footer">
                <a href="{{ path('app_game_show', {id: game.id}) }}" class="btn btn--primary btn--small">
                    Voir détails
                </a>
            </div>
        </div>
        {% endfor %}
    </div>
</div>
{% endblock %}
```

### Formulaire centré

```twig
{% extends 'base.html.twig' %}

{% block body %}
<div class="d-flex flex--center" style="min-height: 60vh;">
    <div style="max-width: 500px; width: 100%;">
        <div class="card">
            <div class="card__body">
                <h1 class="text--center mb-lg">Nouveau Jeu</h1>
                
                {{ form_start(form) }}
                    <div class="form__group">
                        {{ form_label(form.title, null, {'label_attr': {'class': 'form__label'}}) }}
                        {{ form_widget(form.title, {'attr': {'class': 'form__input'}}) }}
                        {{ form_errors(form.title) }}
                    </div>
                    
                    <div class="text--center mt-md">
                        <button type="submit" class="btn btn--primary btn--large">
                            Enregistrer
                        </button>
                    </div>
                {{ form_end(form) }}
            </div>
        </div>
    </div>
</div>
{% endblock %}
```

## 🚀 Commandes utiles

```bash
# Compiler en mode développement
npm run dev

# Compiler et watch les changements
npm run watch

# Compiler en mode production
npm run build
```

## 💡 Conseils

1. Utilisez toujours le `container` pour centrer le contenu
2. Les grilles s'adaptent automatiquement en responsive
3. Les cards ont un effet hover automatique
4. Les formulaires sont stylés automatiquement
5. Pensez à utiliser les classes d'espacement (mt, mb, py, etc.)
6. Les animations sont activées avec les classes `animate-*`

## 🔧 Personnalisation

Pour personnaliser les couleurs, modifiez les variables dans `retro-theme.scss` :

```scss
$color-dark: #0D0A0B;
$color-cream: #F0F0C9;
$color-purple: #6761A8;
$color-red: #A30015;
$color-teal: #A0C1B9;
```

Puis recompilez avec `npm run build`.

