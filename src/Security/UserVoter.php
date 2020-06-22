<?php

namespace App\Security;

use App\Entity\Acl;
use App\Entity\CallOfProject;
use App\Entity\OrganizingCenter;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{

    const ADMIN_ONE_ORGANIZING_CENTER_AT_LEAST = 'admin_one_organizing_center_at_least';
    const ADMIN_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST = 'admin_one_organizing_center_or_call_of_project_at_least';
    const MANAGE_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST = 'manage_one_organizing_center_or_call_of_project_at_least';
    const AUTH_BASIC = 'auth_basic';

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
        if (!in_array($attribute, [
                self::ADMIN_ONE_ORGANIZING_CENTER_AT_LEAST,
                self::ADMIN_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST,
                self::MANAGE_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST,
                self::AUTH_BASIC,
            ])) {
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
            case self::ADMIN_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST:
                return $this->adminOneOrganizingCenterOrCallOfProjectAtLeast($user);
            case self::MANAGE_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST:
                return $this->manageOneOrganizingCenterOrCallOfProjectAtLeast($user);
            case self::AUTH_BASIC:
                return $this->hasBasicAuth($user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function adminOneOrganizingCenterAtLeast(User $user)
    {
        $acls = $user->getAlcs()->filter(function ($acl) {
            return in_array($acl->getPermission(), CallOfProject::ADMIN_PERMISSIONS) and $acl->getCommon() instanceof OrganizingCenter;
        });

        return $acls->count() > 0;
    }

    private function adminOneOrganizingCenterOrCallOfProjectAtLeast(User $user)
    {
        $acls = $user->getAlcs()->filter(function ($acl) {
            return in_array($acl->getPermission(), CallOfProject::ADMIN_PERMISSIONS) and $acl->getCommon() instanceof CallOfProject;
        });

        return ($acls->count() > 0) or $this->adminOneOrganizingCenterAtLeast($user);
    }

    private function manageOneOrganizingCenterOrCallOfProjectAtLeast(User $user)
    {
        $acls = $user->getAlcs()->filter(function ($acl) {
            return in_array($acl->getPermission(), CallOfProject::EDIT_PERMISSIONS) and $acl->getCommon() instanceof CallOfProject;
        });

        return ($acls->count() > 0) or $this->adminOneOrganizingCenterAtLeast($user);
    }

    private function hasBasicAuth(User $user)
    {
        return $user->getAuth() === User::AUTH_BASIC;
    }

}
