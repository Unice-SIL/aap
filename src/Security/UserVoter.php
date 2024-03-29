<?php

namespace App\Security;

use App\Entity\Acl;
use App\Entity\CallOfProject;
use App\Entity\OrganizingCenter;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{

    const ADMIN_ONE_ORGANIZING_CENTER_AT_LEAST = 'admin_one_organizing_center_at_least';
    const ADMIN_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST = 'admin_one_organizing_center_or_call_of_project_at_least';
    const MANAGE_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST = 'manage_one_organizing_center_or_call_of_project_at_least';
    const VIEW_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST = 'view_one_organizing_center_or_call_of_project_at_least';
    const AUTH_BASIC = 'auth_basic';

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var Security
     */
    private $security;

    /**
     * CallOfUserVoter constructor.
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param Security $security
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, Security $security)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [
                self::ADMIN_ONE_ORGANIZING_CENTER_AT_LEAST,
                self::ADMIN_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST,
                self::MANAGE_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST,
                self::VIEW_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST,
                self::AUTH_BASIC,
            ])) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {

        /** @var User $user */
        $user = $subject;

        if (!$user instanceof User) {
            $user = $token->getUser();
            // the user must be logged in; if not, deny access
            if (!$user instanceof User) return false;
        }

        if ($this->authorizationChecker->isGranted(User::ROLE_ADMIN)) {
            return true;
        }

        switch ($attribute) {
            case self::ADMIN_ONE_ORGANIZING_CENTER_AT_LEAST:
                return $this->adminOneOrganizingCenterAtLeast($user);
            case self::ADMIN_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST:
                return $this->adminOneOrganizingCenterOrCallOfProjectAtLeast($user);
            case self::MANAGE_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST:
                return $this->manageOneOrganizingCenterOrCallOfProjectAtLeast($user);
            case self::VIEW_ONE_ORGANIZING_CENTER_OR_CALL_OF_PROJECT_AT_LEAST:
                return $this->viewOneOrganizingCenterOrCallOfProjectAtLeast($user);
            case self::AUTH_BASIC:
                return $this->hasBasicAuth($user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param User $user
     * @return bool
     */
    private function adminOneOrganizingCenterAtLeast(User $user)
    {
        $acls = $user->getAlcs()->filter(function ($acl) {
            return in_array($acl->getPermission(), CallOfProject::ADMIN_PERMISSIONS) and $acl->getCommon() instanceof OrganizingCenter;
        });

        return $acls->count() > 0;
    }

    /**
     * @param User $user
     * @return bool
     */
    private function adminOneOrganizingCenterOrCallOfProjectAtLeast(User $user)
    {
        $acls = $user->getAlcs()->filter(function ($acl) {
            return in_array($acl->getPermission(), CallOfProject::ADMIN_PERMISSIONS) and $acl->getCommon() instanceof CallOfProject;
        });

        return ($acls->count() > 0) or $this->adminOneOrganizingCenterAtLeast($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    private function manageOneOrganizingCenterOrCallOfProjectAtLeast(User $user)
    {
        $acls = $user->getAlcs()->filter(function ($acl) {
            return in_array($acl->getPermission(), CallOfProject::EDIT_PERMISSIONS) and $acl->getCommon() instanceof CallOfProject;
        });

        return ($acls->count() > 0) or $this->adminOneOrganizingCenterAtLeast($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    private function viewOneOrganizingCenterOrCallOfProjectAtLeast(User $user)
    {
        $acls = $user->getAlcs()->filter(function ($acl) {
            return in_array($acl->getPermission(), CallOfProject::VIEWER_PERMISSIONS) and $acl->getCommon() instanceof CallOfProject;
        });

        return ($acls->count() > 0) or $this->manageOneOrganizingCenterOrCallOfProjectAtLeast($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    private function hasBasicAuth(User $user)
    {
        return $user->getAuth() === User::AUTH_BASIC;
    }

}
