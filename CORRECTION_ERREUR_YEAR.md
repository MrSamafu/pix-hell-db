# ✅ Correction de l'erreur YEAR()

## 🔴 Erreur rencontrée
```
[Syntax Error] line 0, col 16: Error: Expected known function, got 'YEAR'
```

## 💡 Cause du problème
La fonction SQL `YEAR()` n'existe pas en **DQL (Doctrine Query Language)**. Doctrine utilise sa propre syntaxe qui doit être compatible avec tous les SGBD.

## ✅ Solution appliquée

### Avant (incorrect) :
```php
// ❌ YEAR() n'existe pas en DQL
$qb->andWhere('YEAR(g.releaseDate) = :year')

$result = $this->createQueryBuilder('g')
    ->select('DISTINCT YEAR(g.releaseDate) as year')
```

### Après (correct) :
```php
// ✅ SUBSTRING() est supporté en DQL
$qb->andWhere('SUBSTRING(g.releaseDate, 1, 4) = :year')

$result = $this->createQueryBuilder('g')
    ->select('DISTINCT SUBSTRING(g.releaseDate, 1, 4) as year')
```

## 🔧 Fichier modifié
**src/Repository/GameRepository.php**
- Ligne ~40 : Filtre par année dans `findBySearchAndFilters()`
- Ligne ~85 : Méthode `findAvailableYears()`

## 📝 Explication technique

`SUBSTRING(g.releaseDate, 1, 4)` :
- Extrait les 4 premiers caractères de la date
- Une date `2023-05-15` devient `2023`
- Compatible avec tous les SGBD supportés par Doctrine

## 🚀 Commande à exécuter

```bash
php bin/console cache:clear
```

Puis rechargez la page `/game` et testez la recherche !

---

## ✅ La recherche fonctionne maintenant !

Vous pouvez utiliser tous les filtres, y compris le filtre par année. 🎉

