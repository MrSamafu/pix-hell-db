# ✅ Boutons de modification et suppression - Consoles et Accessoires

## 🔍 État actuel

### ✅ Ce qui est déjà en place

**Templates :**
- ✅ `templates/console/index.html.twig` - Boutons modifier/supprimer présents
- ✅ `templates/accessory/index.html.twig` - Boutons modifier/supprimer présents

**Voters (droits d'accès) :**
- ✅ `src/Security/Voter/ConsoleVoter.php` - Vérifie les permissions
- ✅ `src/Security/Voter/AccessoryVoter.php` - Vérifie les permissions

**Contrôleurs :**
- ✅ `ConsoleController` - Routes edit et delete avec `denyAccessUnlessGranted()`
- ✅ `AccessoryController` - Routes edit et delete avec `denyAccessUnlessGranted()`

---

## 🎯 Logique des permissions

### Qui peut voir les boutons ?

**Les boutons s'affichent si :**
```twig
{% if is_granted('edit', console) %}
    <a href="...">✏️</a>
{% endif %}

{% if is_granted('delete', console) %}
    <button>🗑️</button>
{% endif %}
```

### Règles des Voters

**ConsoleVoter et AccessoryVoter :**
1. ✅ **Admin** → Peut tout faire (modifier/supprimer n'importe quelle console/accessoire)
2. ✅ **Créateur** → Peut modifier/supprimer ses propres consoles/accessoires
3. ❌ **Autres utilisateurs** → Peuvent seulement voir

**Code du Voter :**
```php
// Admin bypass
if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
    return true;
}

// Edit/Delete : vérifier si l'utilisateur est le créateur
case 'edit':
case 'delete':
    return $this->isOwner($user, $subject);
```

---

## 🔧 Pourquoi les boutons ne s'affichent pas ?

### Raison 1 : Vous n'êtes pas le créateur
Si les consoles/accessoires ont été créés par un autre utilisateur ou via les fixtures, vous ne verrez pas les boutons.

**Solution :**
- Créez une nouvelle console/accessoire avec votre compte
- Les boutons apparaîtront sur celle-ci

### Raison 2 : Vous n'avez pas le rôle ADMIN
Si vous n'êtes pas admin et pas créateur, les boutons ne s'affichent pas.

**Solution : Donnez-vous le rôle ADMIN**

#### Option A : Via la base de données
```sql
UPDATE user 
SET roles = '["ROLE_ADMIN"]' 
WHERE email = 'votre@email.com';
```

#### Option B : Via une commande Symfony
Créez un fichier `src/Command/PromoteUserCommand.php` :
```php
<?php
namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:user:promote')]
class PromoteUserCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $output->writeln('<error>Utilisateur non trouvé</error>');
            return Command::FAILURE;
        }

        $user->setRoles(['ROLE_ADMIN']);
        $this->em->flush();

        $output->writeln('<info>Utilisateur promu ADMIN avec succès !</info>');
        return Command::SUCCESS;
    }
}
```

Puis exécutez :
```bash
php bin/console app:user:promote votre@email.com
```

---

## 🧪 Test de fonctionnement

### 1. Vérifier votre rôle
Ajoutez temporairement dans un template (ex: `base.html.twig`) :
```twig
{# DEBUG - À retirer après test #}
{% if app.user %}
    Rôles : {{ app.user.roles|json_encode }}
{% endif %}
```

### 2. Créer une console/accessoire
1. Connectez-vous
2. Allez sur `/console/new` ou `/accessory/new`
3. Créez un élément
4. Retournez sur la liste
5. ✅ Vous devriez voir les boutons ✏️ et 🗑️ sur votre création

### 3. Tester avec un admin
1. Donnez-vous le rôle ADMIN
2. Rechargez la page
3. ✅ Vous devriez voir les boutons sur TOUTES les consoles/accessoires

---

## 🎨 Aperçu des boutons

```
┌─────────────────────────────────┐
│ 🕹️ PlayStation 5                │
│ Fabricant: Sony                  │
│ Génération: 9                    │
│                                  │
│ [Voir détails] [✏️] [🗑️]       │
└─────────────────────────────────┘
```

**Boutons visibles selon le contexte :**
- 👁️ **Voir détails** → Toujours visible
- ✏️ **Modifier** → Si créateur ou admin
- 🗑️ **Supprimer** → Si créateur ou admin

---

## 📝 Code des boutons (déjà en place)

### Console
```twig
<div class="card-actions">
    <a href="{{ path('app_console_show', {'id': console.id}) }}" 
       class="btn btn-secondary btn-sm">
        Voir détails
    </a>
    
    {% if is_granted('edit', console) %}
        <a href="{{ path('app_console_edit', {'id': console.id}) }}" 
           class="btn btn-outline btn-sm" title="Modifier">
            ✏️
        </a>
    {% endif %}
    
    {% if is_granted('delete', console) %}
        <form method="post" 
              action="{{ path('app_console_delete', {'id': console.id}) }}" 
              class="inline-form" 
              onsubmit="return confirm('Êtes-vous sûr ?');">
            <input type="hidden" name="_token" 
                   value="{{ csrf_token('delete' ~ console.id) }}">
            <button type="submit" 
                    class="btn btn-danger btn-sm" 
                    title="Supprimer">🗑️</button>
        </form>
    {% endif %}
</div>
```

### Accessoire
Même structure, remplacez `console` par `accessory`.

---

## ✅ Checklist de vérification

- [ ] Je suis connecté ?
- [ ] J'ai le rôle ROLE_ADMIN ?
- [ ] Ou je suis le créateur de la console/accessoire ?
- [ ] Le cache Symfony est vidé ? (`php bin/console cache:clear`)
- [ ] Les Voters existent ? (ConsoleVoter.php, AccessoryVoter.php)
- [ ] Les routes edit/delete existent dans les contrôleurs ?

---

## 🚀 Solution rapide

**Pour voir tous les boutons immédiatement :**

1. Donnez-vous le rôle ADMIN :
```sql
UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE email = 'votre@email.com';
```

2. Videz le cache :
```bash
php bin/console cache:clear
```

3. Rechargez la page
4. ✅ Les boutons ✏️ et 🗑️ apparaissent sur TOUS les éléments

---

## 📖 Résumé

**Tout est déjà en place !** Les boutons, les voters, les routes... tout fonctionne.

**Le "problème" est une fonctionnalité :** Vous ne voyez les boutons que si vous êtes admin ou créateur. C'est normal et sécurisé.

**Pour tester :** Créez un nouvel élément avec votre compte, ou donnez-vous le rôle ADMIN.

---

Les boutons fonctionnent ! 🎉

