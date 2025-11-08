# 🎮 PixHellDB - Système de Design Retrogaming

## 📋 Résumé

Système de design complet pour votre application de gestion de collection retrogaming, avec :
- ✅ Thème sombre moderne avec palette de 5 couleurs
- ✅ Composants UI complets (cards, buttons, forms, alerts, etc.)
- ✅ 100% Responsive (Mobile, Tablette, Desktop)
- ✅ Animations et effets retro
- ✅ Accessible et optimisé

## 🎨 Palette de Couleurs

| Couleur | Hex | Usage |
|---------|-----|-------|
| **Dark** | `#0D0A0B` | Fond principal |
| **Cream** | `#F0F0C9` | Texte principal |
| **Purple** | `#6761A8` | Couleur primaire (boutons, liens) |
| **Red** | `#A30015` | Couleur accent (actions importantes) |
| **Teal** | `#A0C1B9` | Couleur secondaire (infos, success) |

## 📁 Fichiers créés

```
assets/styles/
├── app.scss              # Point d'entrée principal
├── retro-theme.scss      # Thème de base complet
└── enhancements.scss     # Composants avancés et effets

templates/
├── base.html.twig        # Template de base (mis à jour)
├── home/
│   └── index.html.twig   # Page d'accueil (mis à jour)
└── security/
    └── login.html.twig   # Page de connexion (mise à jour)

Documentation/
├── THEME_GUIDE.md        # Guide complet des classes CSS
└── INSTALLATION_THEME.md # Instructions d'installation
```

## 🚀 Démarrage rapide

### 1. Compiler les assets

```bash
# En mode développement avec watch (recommandé pendant le développement)
npm run watch

# Ou en mode production (pour déploiement)
npm run build
```

### 2. Visualiser le résultat

```bash
# Lancer le serveur Symfony
symfony server:start
```

Puis ouvrez http://localhost:8000 dans votre navigateur.

## 💻 Exemple d'utilisation

### Page avec grille de cards

```twig
{% extends 'base.html.twig' %}

{% block body %}
<div class="container">
    <h1 class="page-title">Mes Jeux Vidéo</h1>
    
    <div class="grid grid--3">
        {% for game in games %}
        <div class="card">
            {% if game.image %}
                <img src="{{ game.image }}" class="card__image" alt="{{ game.title }}">
            {% endif %}
            <div class="card__body">
                <h3 class="card__title">{{ game.title }}</h3>
                <p class="card__text">{{ game.developer }}</p>
                <div class="mt-sm">
                    {% for genre in game.genres %}
                        <span class="tag">{{ genre.name }}</span>
                    {% endfor %}
                </div>
            </div>
            <div class="card__footer">
                <a href="{{ path('app_game_show', {id: game.id}) }}" class="btn btn--primary btn--small">
                    Voir
                </a>
            </div>
        </div>
        {% endfor %}
    </div>
</div>
{% endblock %}
```

### Formulaire stylé

```twig
<form method="post">
    <div class="form__group">
        <label class="form__label">Titre du jeu</label>
        <input type="text" name="title" class="form__input" placeholder="Entrez le titre">
    </div>
    
    <div class="form__group">
        <label class="form__label">Description</label>
        <textarea name="description" class="form__textarea"></textarea>
    </div>
    
    <button type="submit" class="btn btn--primary">
        <i class="fas fa-save"></i> Enregistrer
    </button>
</form>
```

## 🎯 Composants disponibles

- **Navigation** : Menu responsive avec toggle mobile
- **Cards** : Cards modulaires avec image, body, footer
- **Buttons** : 4 variantes (primary, secondary, accent, outline)
- **Forms** : Inputs, textarea, select stylés
- **Alerts** : Success, error, info
- **Badges** : Tags colorés
- **Tables** : Tableaux responsives
- **Grilles** : 2, 3 ou 4 colonnes responsive
- **Animations** : Fade in, slide in

## 📱 Breakpoints

- **Mobile** : < 576px
- **Tablet** : 576px - 1024px  
- **Desktop** : > 1024px
- **Wide** : > 1440px

## 📚 Documentation complète

Consultez **THEME_GUIDE.md** pour :
- Liste exhaustive de toutes les classes
- Exemples d'utilisation détaillés
- Guide de personnalisation
- Bonnes pratiques

## 🔧 Personnalisation

Pour modifier les couleurs, éditez `assets/styles/retro-theme.scss` :

```scss
// Variables de couleurs
$color-dark: #0D0A0B;
$color-cream: #F0F0C9;
$color-purple: #6761A8;
$color-red: #A30015;
$color-teal: #A0C1B9;
```

Puis recompilez : `npm run build`

## ✨ Fonctionnalités bonus

### Enhancements inclus

- 🎨 Scrollbar personnalisée
- ✨ Effets néon sur les liens
- 📺 Effet scanline rétro sur les cards
- 🎯 Focus amélioré pour l'accessibilité
- 🔄 Loader animé
- 🍞 Toast notifications
- 📊 Stat cards
- 🎪 Empty state
- 🏷️ Tags pour genres/modes

### Animations

- `animate-fade-in` : Apparition en fondu
- `animate-slide-in` : Glissement depuis le bas
- Hover effects sur les cards
- Transitions fluides partout

## 🎮 Templates mis à jour

Les templates suivants ont été modernisés :
- ✅ `base.html.twig` : Navigation + structure
- ✅ `home/index.html.twig` : Page d'accueil avec derniers ajouts
- ✅ `login.html.twig` : Formulaire de connexion centré

## 📝 TODO : Templates à adapter

Pour finaliser le design, adaptez ces templates avec les nouvelles classes :

- `templates/game/index.html.twig`
- `templates/game/show.html.twig`
- `templates/game/new.html.twig`
- `templates/game/edit.html.twig`
- `templates/console/index.html.twig`
- `templates/console/show.html.twig`
- `templates/accessory/index.html.twig`
- `templates/accessory/show.html.twig`
- `templates/profile/index.html.twig`
- `templates/collection/index.html.twig`

## 🚀 Performance

### Optimisations incluses

- ✅ CSS compilé et minifié en production
- ✅ Transitions optimisées avec GPU
- ✅ Images lazy loading ready
- ✅ Responsive images support

### En production

```bash
npm run build
```

Génère des assets optimisés dans `/public/build/`

## 🤝 Contribution

Le système de design est modulaire :
- Ajoutez vos composants dans `enhancements.scss`
- Utilisez les variables de `retro-theme.scss` pour la cohérence
- Suivez la convention BEM pour les noms de classes

## 📞 Support

- **Guide des classes** : THEME_GUIDE.md
- **Installation** : INSTALLATION_THEME.md
- **Code source** : assets/styles/

---

**🎮 PixHellDB - Made with ❤️ for retrogamers**

