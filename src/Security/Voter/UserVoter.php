<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    private $security = null;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['FORMATEUR_EDIT', 'APPRENANT_VIEW', 'FORMATEUR_VIEW', 'APPRENANT_VIEW', 'ADMIN_VIEW', 'ADMIN_EDIT'])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!($user instanceof UserInterface)) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            // logic to determine if the user can EDIT
            // return true or false
            case 'FORMATEUR_EDIT':
                if ($this->security->isGranted('ROLE_FORMATEUR') && $subject === $user) {
                    return true;
                }
                return false;
            case 'APPRENANT_EDIT':
                if ($this->security->isGranted('ROLE_FORMATEUR') || $subject === $user) {
                    return true;
                }
                return false;
            case 'ADMIN_EDIT':
                if ($this->security->isGranted('ROLE_ADMIN')  && $subject === $user) {
                    return true;
                }
                return false;
            // logic to determine if the user can VIEW
            // return true or false
            case 'FORMATEUR_VIEW':
                if ($this->security->isGranted('ROLE_CM') || ($this->security->isGranted('ROLE_FORMATEUR') && $subject === $user)) {
                    return true;
                }
                return false;
            case 'APPRENANT_VIEW':
                if ($this->security->isGranted('ROLE_CM') || $this->security->isGranted('ROLE_FORMATEUR') || ($this->security->isGranted('ROLE_APPRENANT') && $subject === $user)) {
                    return true;
                }
                return false; 
            case 'ADMIN_VIEW':
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                return false; 
        }

        return false;
    }
}
