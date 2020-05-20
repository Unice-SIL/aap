<?php

namespace App\Security;

use App\Entity\Acl;
use App\Entity\OrganizingCenter;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{

    const ADMIN_ONE_ORGANIZING_CENTER_AT_LEAST = 'admin_one_organizing_center_at_least';

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * CallOfUserVoter constructor.
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::ADMIN_ONE_ORGANIZING_CENTER_AT_LEAST])) {
            return false;
        }

        // only vote on `User` objects
        if (!$subject instanceof User) {
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

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var User $user */
        $user = $subject;

        switch ($attribute) {
            case self::ADMIN_ONE_ORGANIZING_CENTER_AT_LEAST:
                return $this->adminOneOrganizingCenterAtLeast($user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function adminOneOrganizingCenterAtLeast(User $user)
    {
        $acls = $user->getAlcs()->filter(function ($acl) {
            return $acl->getPermission() === Acl::PERMISSION_ADMIN and $acl->getCommon() instanceof OrganizingCenter;
        });

        return $acls->count() > 0;
    }

}
