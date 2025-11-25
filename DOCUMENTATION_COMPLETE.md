# 📚 Documentation Complète - Pix Hell DB

## 📋 Table des matières

1. [Système de Profils et Badges](#système-de-profils-et-badges)
2. [Espace d'Administration](#espace-dadministration)
3. [Description des Badges](#description-des-badges)
4. [Améliorations Front Admin & Badges](#améliorations-front-admin--badges)

---

# 1. Système de Profils et Badges

## ✅ Fichiers créés

### Entités et Repositories
1. **src/Entity/Badge.php** - Entité Badge avec relation ManyToMany vers User
2. **src/Repository/BadgeRepository.php** - Repository pour Badge

### Controllers
3. **src/Controller/BadgeController.php** - Gestion CRUD des badges (admin uniquement)
4. Modification de **src/Controller/ProfileController.php** - Ajout des routes pour :
   - Voir le profil d'un utilisateur
   - Attribuer/retirer des badges (admin uniquement)

### Formulaires
5. **src/Form/BadgeType.php** - Formulaire pour créer/éditer un badge
6. Modification de **src/Form/ProfileType.php** - Ajout des nouveaux champs (prénom, nom, bio, dates)
7. Modification de **src/Form/RegistrationFormType.php** - Ajout des champs optionnels lors de l'inscription

### Templates
8. **templates/badge/index.html.twig** - Liste des badges (admin)
9. **templates/badge/new.html.twig** - Création d'un badge (admin)
10. **templates/badge/edit.html.twig** - Modification d'un badge (admin)
11. **templates/profile/user_profile.html.twig** - Profil public d'un utilisateur avec gestion badges
12. **templates/collection/users.html.twig** - Liste de tous les utilisateurs
13. Modification de **templates/profile/show.html.twig** - Affichage des nouveaux champs et badges
14. Modification de **templates/profile/edit.html.twig** - Édition des nouveaux champs
15. Modification de **templates/security/register.html.twig** - Formulaire d'inscription avec nouveaux champs

### Migrations
16. **migrations/Version20251125000000.php** - Migration pour :
   - Créer la table `badge`
   - Créer la table de liaison `user_badge`
   - Ajouter les champs à la table `user` (firstName, lastName, bio, birthDate, associationJoinDate)

## 📋 Nouveaux champs User

### Champs obligatoires (lors de l'inscription)
- ✅ email
- ✅ username
- ✅ password

### Champs optionnels
- firstName (Prénom)
- lastName (Nom)
- bio (Biographie)
- birthDate (Date de naissance)
- associationJoinDate (Date d'arrivée dans l'association)
- badges (Collection de badges - relation ManyToMany)

## 🎮 Fonctionnalités badges

### Pour les admins (ROLE_ADMIN)
- Créer des badges (nom + URL image)
- Modifier des badges
- Supprimer des badges
- Attribuer des badges aux utilisateurs
- Retirer des badges aux utilisateurs
- Accès via : `/admin/badge`

### Pour tous les utilisateurs
- Voir les badges sur les profils
- Voir les badges dans la liste des utilisateurs

## 🔗 Routes ajoutées

| Route | Chemin | Accès | Description |
|-------|--------|-------|-------------|
| app_badge_index | /admin/badge | ADMIN | Liste des badges |
| app_badge_new | /admin/badge/new | ADMIN | Créer un badge |
| app_badge_edit | /admin/badge/{id}/edit | ADMIN | Modifier un badge |
| app_badge_delete | /admin/badge/{id} | ADMIN | Supprimer un badge |
| app_user_profile | /user/{id} | USER | Voir le profil d'un utilisateur |
| app_user_badge_add | /user/{id}/badge/add/{badgeId} | ADMIN | Attribuer un badge |
| app_user_badge_remove | /user/{id}/badge/remove/{badgeId} | ADMIN | Retirer un badge |
| app_collection_users | /collection/users | USER | Liste des utilisateurs |

## 🎨 Design des pages de profil

### 📄 **1. Page Mon Profil** (`show.html.twig`)
Amélioration complète avec :

#### 📋 Design
- **Header moderne** avec avatar circulaire, niveau de l'utilisateur et gradient rétro
- **Statistiques visuelles** : 3 cartes affichant le nombre de jeux, consoles et accessoires
- **Sections organisées** : Informations personnelles et badges bien séparés
- **Badges grid** : Affichage élégant des badges avec effet hover

#### 🎨 Fonctionnalités visuelles
- Avatar avec icône astronaute
- Badge de niveau calculé dynamiquement (total des items dans la collection)
- Cartes de statistiques avec icônes et animations au survol
- Sections avec en-têtes colorés (purple/cyan)
- Grille responsive pour les badges
- Boutons d'action bien visibles

### ✏️ **2. Formulaire d'Édition** (`edit.html.twig`)
Refonte complète avec :

#### 📝 Organisation du formulaire
Le formulaire est divisé en **4 sections logiques** :

1. **Informations de compte** (Obligatoire)
   - Username et Email
   - Badge "Obligatoire" en rouge
   - Aide contextuelle pour chaque champ

2. **Identité** (Optionnel)
   - Prénom, Nom
   - Date de naissance
   - Date d'arrivée dans l'association
   - Badge "Optionnel" en gris

3. **À propos de vous** (Optionnel)
   - Bio avec textarea grande
   - Placeholder encourageant
   - Aide pour guider l'utilisateur

4. **Sécurité** (Sensible)
   - Changement de mot de passe
   - Badge "Sensible" en orange
   - Alert box explicative

### 👤 **3. Profil Public** (`user_profile.html.twig`)
Design similaire à show.html.twig avec ajouts :

#### 👤 Spécificités profil public
- Avatar différent (ninja) pour distinguer du profil perso
- Email visible uniquement pour les admins
- Section d'administration pour les admins :
  - Attribution de badges
  - Retrait de badges avec confirmation
  - Liste des badges disponibles à attribuer

---

# 2. Espace d'Administration

## ✅ Ce qui a été créé

### 📂 Nouveau Controller
**src/Controller/AdminController.php**
- Dashboard administrateur avec statistiques
- Liste complète des utilisateurs
- Modification des rôles utilisateur (promouvoir/rétrograder admin)
- Gestion des badges par utilisateur
- Toutes les routes protégées par `#[IsGranted('ROLE_ADMIN')]`

### 🎨 Nouveaux Templates

#### 1. **templates/admin/dashboard.html.twig**
Tableau de bord administrateur avec :
- 📊 Cartes de statistiques (utilisateurs, badges, jeux, consoles)
- 🎯 Actions rapides vers toutes les sections admin
- 🎮 Liens vers la gestion du contenu (jeux, consoles, accessoires)
- 📈 Section statistiques (à venir)
- Design moderne avec gradient rouge/purple

#### 2. **templates/admin/users.html.twig**
Gestion complète des utilisateurs avec :
- 📋 Tableau listant tous les utilisateurs
- 👤 Avatar, nom, email pour chaque utilisateur
- 🏆 Badge de rôle (Admin/Utilisateur)
- 📊 Statistiques de collection par utilisateur
- ⚙️ Actions :
  - Voir le profil
  - Gérer les badges
  - Promouvoir/Rétrograder admin
- Design responsive avec tableau scrollable

#### 3. **templates/admin/user_badges.html.twig**
Gestion des badges d'un utilisateur spécifique avec :
- 👤 Carte d'information utilisateur
- 🏆 Section badges actuels (avec bouton retirer)
- ➕ Section badges disponibles (avec bouton attribuer)
- 🔗 Liens rapides vers création/gestion de badges
- Grid responsive pour l'affichage des badges

### 🔄 Fichiers modifiés

#### 1. **templates/base.html.twig**
- ✅ Ajout du lien "Admin" dans le menu de navigation
- 🎯 Visible uniquement pour les utilisateurs avec `ROLE_ADMIN`
- 🎨 Style spécial gradient rouge/purple pour le lien

#### 2. **assets/styles/retro-theme.scss**
- ✅ Ajout du style `navbar__link--admin`
- 🎨 Gradient rouge/purple avec effet hover
- ✨ Glow cyan au survol

#### 3. **templates/badge/index.html.twig**
- 🎨 Amélioration complète du design
- 📊 Header avec gradient et statistiques
- 🎯 Grid moderne pour l'affichage des badges
- 🚀 État vide stylisé si aucun badge

## 🗺️ Routes créées

| Route | Chemin | Méthode | Description |
|-------|--------|---------|-------------|
| `app_admin_dashboard` | `/admin` | GET | Dashboard principal admin |
| `app_admin_users` | `/admin/users` | GET | Liste de tous les utilisateurs |
| `app_admin_user_toggle_admin` | `/admin/user/{id}/toggle-admin` | POST | Promouvoir/Rétrograder admin |
| `app_admin_user_manage_badges` | `/admin/user/{id}/manage-badges` | GET | Gérer les badges d'un user |

## 🎯 Fonctionnalités par page

### Dashboard Admin (`/admin`)
- ✅ Vue d'ensemble des statistiques
- ✅ Accès rapide à toutes les sections
- ✅ Cards cliquables pour :
  - Gestion des utilisateurs
  - Gestion des badges
  - Gestion du contenu (jeux, consoles, accessoires)

### Liste des utilisateurs (`/admin/users`)
- ✅ Tableau avec toutes les infos utilisateurs
- ✅ Avatar généré automatiquement
- ✅ Badge de rôle (Admin/User)
- ✅ Statistiques de collection
- ✅ Actions :
  - 👁️ Voir le profil complet
  - 🏆 Gérer les badges
  - 🔄 Toggle admin (promouvoir/rétrograder)

### Gestion des badges utilisateur (`/admin/user/{id}/manage-badges`)
- ✅ Affichage des badges actuels
- ✅ Bouton pour retirer un badge
- ✅ Affichage des badges disponibles
- ✅ Bouton pour attribuer un badge
- ✅ Message si tous les badges sont attribués

## 🔐 Sécurité

### Protection des routes
```php
#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
```

### Tokens CSRF
- Tous les formulaires protégés par CSRF
- Token unique par utilisateur et action
- Vérification côté serveur systématique

### Confirmations
- Confirmation JavaScript avant suppression
- Confirmation avant modification de rôle
- Messages flash pour feedback utilisateur

---

# 3. Description des Badges

## ✅ Modifications effectuées

### 🗄️ Base de données

#### Entité Badge
✅ Ajout de la propriété `description`
```php
#[ORM\Column(type: 'text', nullable: true)]
private ?string $description = null;
```

#### Migration (`migrations/Version20251125000001.php`)
✅ Migration créée pour ajouter la colonne `description` à la table `badge`
```sql
ALTER TABLE badge ADD description LONGTEXT DEFAULT NULL
```

### 📋 Formulaires

#### BadgeType
✅ Ajout du champ `description` de type `TextareaType`
- Label : "Description"
- Obligatoire : Non (optionnel)
- Rows : 4
- Placeholder : "Décrivez ce badge et comment l'obtenir..."

### 🎨 Templates mis à jour

1. **badge/new.html.twig** - Ajout du champ description
2. **badge/edit.html.twig** - Ajout du champ description
3. **badge/index.html.twig** - Affichage avec troncature à 80 caractères
4. **admin/user_badges.html.twig** - Description complète
5. **profile/show.html.twig** - Tooltip avec description
6. **profile/user_profile.html.twig** - Tooltip avec description

## 🎯 Fonctionnalités

### Affichage aux utilisateurs

#### Dans la liste admin des badges
- Description affichée sous le nom
- Tronquée à 80 caractères si trop longue
- Style discret mais lisible

#### Dans la gestion des badges utilisateur
- Description complète affichée
- Visible pour les badges actuels ET disponibles

#### Sur les profils utilisateurs
- **Tooltip au survol** : Description complète
- **Icône d'information** : Indique qu'il y a une description
- **UX améliorée** : Discret mais informatif

### Exemples de descriptions
```
"Badge attribué aux membres fondateurs de l'association"
"Obtenu en atteignant 100 jeux dans sa collection"
"Récompense pour participation active aux événements"
```

---

# 4. Améliorations Front Admin & Badges

## ✅ Fichiers modifiés

### 📋 Templates Badges

#### 1. **badge/new.html.twig** - Création de badge
✨ **Transformations complètes** :
- **Header admin stylisé** : Gradient rouge/purple avec icône
- **Breadcrumb visuel** : Titre + description + bouton retour
- **Formulaire en sections** : Chaque champ avec icône et aide contextuelle
- **Preview du badge** : Zone d'aperçu pour visualiser l'image
- **Carte d'aide** : Conseils pour créer un bon badge

#### 2. **badge/edit.html.twig** - Modification de badge
✨ **Transformations complètes** :
- **Badge actuel** : Carte cyan montrant le badge avant modification
- **Formulaire d'édition** : Style cohérent avec création
- **Danger Zone** : Section rouge pour suppression

#### 3. **badge/index.html.twig** - Liste des badges
✨ **Améliorations** :
- **Images avec effet hover** : Scale 1.1 au survol
- **Overlay scanlines** : Effet rétro sur les images
- **Shadow sur images** : Drop-shadow pour profondeur

### 🛡️ Templates Admin

#### 4. **admin/dashboard.html.twig** - Dashboard
✨ **Améliorations** :
- **Cartes statistiques** : Pattern diagonal en fond
- **Icônes animées** : Scale + rotation au hover
- **Cartes d'action** : Effet shine qui traverse au hover

#### 5. **admin/users.html.twig** - Liste utilisateurs
✨ **Améliorations** :
- **Headers de tableau** : Ligne cyan animée au hover
- **Lignes de tableau** : Barre cyan à gauche au hover
- **Avatars animés** : Shimmer effect + scale au hover
- **Boutons d'action** : Effet ripple au clic

## 🎨 Nouveaux composants CSS créés

### 1. **page-header-admin**
Header standardisé pour toutes les pages admin
- Gradient rouge/purple
- Padding généreux (2rem)
- Border-radius 15px

### 2. **form-card**
Container pour les formulaires admin
- Background dark
- Border purple 2px
- Header avec fond purple

### 3. **form-label-retro**
Labels de formulaire stylisés
- Couleur cyan
- Font-weight bold
- Icônes FontAwesome

### 4. **form-control-retro**
Inputs stylisés rétro
- Background dark transparent
- Border purple 2px
- Focus: border cyan + glow

### 5. **current-badge-card**
Carte d'affichage du badge actuel
- Border cyan 2px
- Display flex pour layout

### 6. **badge-preview-section**
Zone de prévisualisation
- Background gradient
- Border purple
- Centré

### 7. **help-card**
Carte d'aide et conseils
- Background cyan transparent
- Border cyan
- Display flex

### 8. **danger-zone**
Section de suppression
- Background red transparent
- Border red
- Alert visuel

## ✨ Animations et effets ajoutés

### 1. **Shimmer Animation**
Effet de brillance sur les avatars
```css
@keyframes shimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}
```

### 2. **Hover Effects**
- `transform: translateY(-5px)` - Cartes
- `transform: scale(1.1)` - Icônes
- `border-color: cyan` - Changement de couleur

### 3. **Ripple Effect**
Sur les boutons d'action
```css
.btn::before {
    width: 0 → 300px au hover
    background: rgba(255,255,255,0.3)
}
```

### 4. **Scanlines**
Effet rétro sur les images
```css
repeating-linear-gradient(
    0deg,
    rgba(255,255,255,0.03) 0px,
    transparent 2px
)
```

### 5. **Diagonal Pattern**
Sur les cartes de stats
```css
repeating-linear-gradient(
    45deg,
    rgba(103,97,168,0.05) 0px,
    transparent 20px
)
```

### 6. **Shine Effect**
Sur les cartes admin qui traverse au hover

## 🎯 Cohérence visuelle

### Palette de couleurs
```
--retro-dark: #0D0A0B (fond)
--retro-light: #F0F0C9 (texte)
--retro-purple: #6761A8 (principal)
--retro-red: #A30015 (accent danger)
--retro-cyan: #A0C1B9 (accent secondaire)
```

### Typographie
- **Titres** : Font-weight 700, icons FontAwesome
- **Labels** : Uppercase, letterspacing, cyan
- **Texte** : Font-size 1rem, line-height 1.6
- **Hints** : Font-size 0.85rem, opacity 0.6

### Espacements
- **Padding cards** : 2rem
- **Gap entre éléments** : 1rem
- **Border-radius** : 10-15px
- **Margins verticales** : 1.5-2rem

## 📱 Responsive

### Desktop (> 1024px)
- Layout complet avec toutes les colonnes
- Formulaires en 1 colonne large
- Boutons côte à côte

### Tablette (768px - 1024px)
- Formulaires adaptés
- Boutons empilés
- Gap réduit

### Mobile (< 768px)
- Tout en colonne
- Headers centrés
- Boutons full-width

---

# 📊 Structure de la base de données

## Table `badge`
```sql
CREATE TABLE badge (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    description LONGTEXT DEFAULT NULL
);
```

## Table `user_badge` (Many-to-Many)
```sql
CREATE TABLE user_badge (
    user_id INT NOT NULL,
    badge_id INT NOT NULL,
    PRIMARY KEY(user_id, badge_id),
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badge(id) ON DELETE CASCADE
);
```

## Table `user` (nouveaux champs)
```sql
ALTER TABLE user ADD (
    first_name VARCHAR(255) DEFAULT NULL,
    last_name VARCHAR(255) DEFAULT NULL,
    bio LONGTEXT DEFAULT NULL,
    birth_date DATE DEFAULT NULL,
    association_join_date DATE DEFAULT NULL
);
```

---

# 🚀 Commandes à exécuter

## 1. Exécuter les migrations
```bash
php bin/console doctrine:migrations:migrate
```

## 2. Vérifier les routes
```bash
php bin/console debug:router | Select-String "admin|badge"
```

## 3. Compiler les assets (si modifiés)
```bash
npm run build
```

## 4. Lancer le serveur
```bash
symfony server:start
```

---

# ✅ Checklist complète

## Base de données
- [x] Entité Badge créée
- [x] Entité User modifiée
- [x] Relations ManyToMany configurées
- [x] Migrations créées
- [x] Colonne description ajoutée

## Controllers
- [x] BadgeController (CRUD badges)
- [x] AdminController (dashboard + users)
- [x] ProfileController (profils + badges)
- [x] Routes protégées par ROLE_ADMIN

## Formulaires
- [x] BadgeType avec description
- [x] ProfileType avec nouveaux champs
- [x] RegistrationFormType avec champs optionnels

## Templates
- [x] Dashboard admin stylisé
- [x] Liste utilisateurs avec tableau
- [x] Gestion badges utilisateur
- [x] CRUD badges avec design cohérent
- [x] Profils utilisateurs améliorés
- [x] Formulaire inscription mis à jour
- [x] Liste des membres

## Design & UX
- [x] Thème rétrogaming appliqué partout
- [x] Animations et transitions fluides
- [x] Effets hover sur tous les éléments
- [x] Icônes FontAwesome
- [x] Responsive complet (desktop/tablette/mobile)
- [x] Tooltips pour descriptions badges
- [x] Feedback visuel (hover, focus, active)

## Sécurité
- [x] Protection routes admin (ROLE_ADMIN)
- [x] Tokens CSRF sur tous les formulaires
- [x] Confirmations avant actions critiques
- [x] Messages flash pour feedback

## Accessibility
- [x] Contraste élevé (WCAG AA)
- [x] Labels explicites
- [x] Focus visible
- [x] Touch targets > 44px

---

# 🎮 Fonctionnalités complètes

## Pour les utilisateurs (ROLE_USER)

### Profil
- ✅ Voir son profil complet avec statistiques
- ✅ Modifier ses informations personnelles
- ✅ Ajouter/modifier prénom, nom, bio, dates
- ✅ Voir ses badges avec descriptions (tooltip)
- ✅ Changer son mot de passe

### Navigation
- ✅ Voir les profils des autres membres
- ✅ Consulter la liste des membres
- ✅ Voir les badges des autres utilisateurs

## Pour les administrateurs (ROLE_ADMIN)

### Dashboard
- ✅ Vue d'ensemble avec statistiques
- ✅ Accès rapide à toutes les sections
- ✅ Lien dans la navbar principale

### Gestion des utilisateurs
- ✅ Voir tous les utilisateurs en tableau
- ✅ Consulter leurs profils
- ✅ Promouvoir/Rétrograder admin
- ✅ Gérer leurs badges

### Gestion des badges
- ✅ Créer des badges avec nom, image, description
- ✅ Modifier des badges existants
- ✅ Supprimer des badges
- ✅ Attribuer badges aux utilisateurs
- ✅ Retirer badges des utilisateurs
- ✅ Voir qui possède quels badges

### Gestion du contenu
- ✅ Accès aux jeux, consoles, accessoires
- ✅ Liens directs depuis le dashboard

---

# 💡 Améliorations futures possibles

## Badges
- [ ] Catégories de badges
- [ ] Badges automatiques (achievements)
- [ ] Historique d'attribution
- [ ] Preview avant création
- [ ] Import/Export de badges

## Utilisateurs
- [ ] Recherche/filtre utilisateurs
- [ ] Export CSV des données
- [ ] Envoi d'emails aux utilisateurs
- [ ] Historique des modifications
- [ ] Suspension de compte

## Statistiques
- [ ] Graphiques de croissance
- [ ] Top des jeux les plus collectionnés
- [ ] Activité récente
- [ ] Rapports mensuels

## Design
- [ ] Upload d'avatar personnalisé
- [ ] Thèmes personnalisés par utilisateur
- [ ] Dark/Light mode
- [ ] Personnalisation du profil

---

# 🎯 Points clés

## Architecture
- ✅ Séparation claire entre user et admin
- ✅ Routes bien organisées et protégées
- ✅ Controllers séparés par responsabilité
- ✅ Templates réutilisables et maintenables

## Sécurité
- ✅ Authentification requise
- ✅ Autorisations basées sur les rôles
- ✅ Protection CSRF
- ✅ Validation côté serveur

## Performance
- ✅ CSS optimisé
- ✅ Animations légères
- ✅ Chargement progressif
- ✅ Images optimisées

## UX/UI
- ✅ Design cohérent
- ✅ Navigation intuitive
- ✅ Feedback immédiat
- ✅ Messages clairs
- ✅ Responsive complet

---

# 📚 Ressources et références

## Technologies utilisées
- **Symfony 6.4+** - Framework PHP
- **Doctrine ORM** - Gestion BDD
- **Twig** - Template engine
- **CSS Custom Properties** - Variables CSS
- **FontAwesome** - Icônes
- **Webpack Encore** - Build assets

## Palette de couleurs
```css
:root {
    --retro-dark: #0D0A0B;
    --retro-light: #F0F0C9;
    --retro-purple: #6761A8;
    --retro-red: #A30015;
    --retro-cyan: #A0C1B9;
}
```

## Conventions de nommage
- **Controllers** : Suffixe `Controller`
- **Entités** : PascalCase
- **Routes** : Snake_case avec préfixe `app_`
- **Templates** : Snake_case, organisés par controller
- **CSS Classes** : BEM-like avec préfixe module

---

# 🎉 Conclusion

L'application **Pix Hell DB** dispose maintenant d'un système complet de :
- ✅ **Gestion des profils** avec informations étendues
- ✅ **Système de badges** pour récompenser les membres
- ✅ **Espace d'administration** moderne et intuitif
- ✅ **Design cohérent** avec le thème rétrogaming
- ✅ **UX optimisée** avec animations et feedback
- ✅ **Sécurité renforcée** avec autorisations
- ✅ **Responsive** sur tous les écrans

Le tout avec un design **professionnel, moderne et fidèle au thème rétrogaming** ! 🎮✨

---

**Date de création** : 25 novembre 2025
**Version** : 1.0
**Auteur** : Documentation générée automatiquement

