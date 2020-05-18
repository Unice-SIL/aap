<?php

namespace App\Security;

use App\Entity\OrganizingCenter;
use App\Entity\User;
use App\Manager\Acl\AbstractAclManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrganizingCenterVoter extends Voter
{

    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::EDIT])) {
            return false;
        }

        // only vote on `OrganizingCenter` objects
        if (!$subject instanceof OrganizingCenter) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var OrganizingCenter $organizingCenter */
        $organizingCenter = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($organizingCenter, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(OrganizingCenter $organizingCenter, User $user)
    {
        $userPermissions = AbstractAclManager::getPermissions($user, $organizingCenter);

        return count(array_intersect($userPermissions->toArray(), OrganizingCenter::EDITOR_PERMISSIONS)) > 0;
    }
}
