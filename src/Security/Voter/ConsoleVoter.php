<?php

namespace App\Security\Voter;

use App\Entity\Console;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ConsoleVoter extends Voter
{
    public const VIEW = 'CONSOLE_VIEW';
    public const CREATE = 'CONSOLE_CREATE';
    public const EDIT = 'CONSOLE_EDIT';
    public const DELETE = 'CONSOLE_DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        // Accepte à la fois les constantes (ex: CONSOLE_VIEW) et les attributs simples en minuscule
        $allowed = [self::VIEW, self::CREATE, self::EDIT, self::DELETE, 'view', 'create', 'edit', 'delete'];
        if (!in_array($attribute, $allowed, true)) {
            return false;
        }

        // La création ne nécessite pas d'objet existant
        if ($attribute === self::CREATE || $attribute === 'create') {
            return true;
        }

        return $subject instanceof Console;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Admin bypass : les utilisateurs avec ROLE_ADMIN peuvent tout faire
        if (method_exists($user, 'getRoles') && in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // Normaliser l'attribut en minuscule pour simplifier les comparaisons
        $attr = strtolower($attribute);

        switch ($attr) {
            case 'console_view':
            case 'view':
            case 'console_create':
            case 'create':
                return true;

            case 'console_edit':
            case 'edit':
            case 'console_delete':
            case 'delete':
                return $this->isOwner($user, $subject);

            default:
                return false;
        }
    }

    private function isOwner(UserInterface $user, $subject): bool
    {
        $owner = null;
        foreach (['getUser', 'getOwner', 'getCreator', 'getCreatedBy', 'getAuthor'] as $method) {
            if (method_exists($subject, $method)) {
                $owner = $subject->$method();
                break;
            }
        }

        if ($owner instanceof UserInterface) {
            return $owner === $user || (method_exists($owner, 'getId') && method_exists($user, 'getId') && $owner->getId() === $user->getId());
        }

        return false;
    }
}
