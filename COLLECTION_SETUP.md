# Commandes pour mettre en place le système de collection

## 1. Appliquer la migration pour ajouter le champ "note"
```bash
php bin/console doctrine:migrations:migrate
```

## 2. Vérifier les routes
```bash
php bin/console debug:router | grep collection
```

Vous devriez voir :
- app_collection_index (GET /collection)
- app_collection_my (GET /collection/my)
- app_collection_user (GET /collection/user/{id})
- app_collection_search (GET /collection/search)
- app_collection_game_update (POST /collection/game/{id}/update)
- app_collection_game_delete (POST /collection/game/{id}/delete)
- app_collection_console_update (POST /collection/console/{id}/update)
- app_collection_console_delete (POST /collection/console/{id}/delete)
- app_collection_accessory_update (POST /collection/accessory/{id}/update)
- app_collection_accessory_delete (POST /collection/accessory/{id}/delete)

## 3. Vider le cache (si nécessaire)
```bash
php bin/console cache:clear
```

## 4. Lancer le serveur de développement
```bash
symfony server:start
```
ou
```bash
php -S localhost:8000 -t public
```

## 5. Accéder aux pages

### Page principale des collections
http://localhost:8000/collection

Cette page affiche 3 parties :
1. **Ma Collection** - Lien vers votre collection personnelle
2. **Utilisateurs** - Liste des utilisateurs avec lien vers leurs collections
3. **Recherche** - Formulaire pour rechercher qui possède un objet

### Votre collection personnelle
http://localhost:8000/collection/my

### Collection d'un utilisateur
http://localhost:8000/collection/user/1
(remplacez 1 par l'ID de l'utilisateur)

### Recherche
http://localhost:8000/collection/search?q=mario&type=game

## Modifications apportées

### Entités mises à jour
- `GameCollection` : ajout du champ `note` (text, nullable)
- `ConsoleCollection` : ajout du champ `note` (text, nullable)
- `AccessoryCollection` : ajout du champ `note` (text, nullable)

### Repositories mis à jour
- `GameCollectionRepository` : ajout de `findUsersWhoOwn()`
- `ConsoleCollectionRepository` : ajout de `findUsersWhoOwn()`
- `AccessoryCollectionRepository` : ajout de `findUsersWhoOwn()`
- `GameRepository` : ajout de `searchByTitle()`
- `ConsoleRepository` : ajout de `searchByName()`
- `AccessoryRepository` : ajout de `searchByName()`

### Controller mis à jour
- `CollectionController` : refonte complète avec nouvelles routes et fonctionnalités

### Templates créés/mis à jour
- `collection/index.html.twig` : page d'accueil avec 3 parties
- `collection/my_collection.html.twig` : ma collection avec gestion (CRUD)
- `collection/user_collection.html.twig` : collection d'un utilisateur (lecture seule)
- `collection/search.html.twig` : recherche dans les collections

### Migration créée
- `Version20251115000000.php` : ajout du champ "note" aux 3 tables de collection

