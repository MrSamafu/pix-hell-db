<?php

namespace App\Security\Voter;

use App\Entity\Accessory;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessoryVoter extends Voter
{
    public const VIEW = 'ACCESSORY_VIEW';
    public const CREATE = 'ACCESSORY_CREATE';
    public const EDIT = 'ACCESSORY_EDIT';
    public const DELETE = 'ACCESSORY_DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        $allowed = [self::VIEW, self::CREATE, self::EDIT, self::DELETE, 'view', 'create', 'edit', 'delete'];
        if (!in_array($attribute, $allowed, true)) {
            return false;
        }

        if ($attribute === self::CREATE || $attribute === 'create') {
            return true; // création ne requiert pas d'objet existant
        }

        return $subject instanceof Accessory;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false; // non connecté
        }

        // Admin bypass
        if (method_exists($user, 'getRoles') && in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        $attr = strtolower($attribute);
        switch ($attr) {
            case 'accessory_view':
            case 'view':
            case 'accessory_create':
            case 'create':
                return true;

            case 'accessory_edit':
            case 'edit':
            case 'accessory_delete':
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
