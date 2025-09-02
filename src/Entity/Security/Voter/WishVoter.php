<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class WishVoter extends Voter
{
    public const EDIT = 'WISH_EDIT';
    public const DELETE = 'WISH_DELETE';

    public function __construct(private Security $security)
    {
    }
    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Wish;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /**
         * @var Wish $subject
         */
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // return true or false
                return $user === $subject->getUser();
                break;

            case self::DELETE:
                return ($user === $subject->getUser() || $this->security->isGranted('ROLE_ADMIN')); ;
                // return true or false
                break;
        }

        return false;
    }
}
