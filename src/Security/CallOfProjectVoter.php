<?php

namespace App\Security;

use App\Entity\CallOfProject;
use App\Entity\Project;
use App\Entity\User;
use App\Manager\Acl\AbstractAclManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CallOfProjectVoter extends Voter
{
    const TO_STUDY_MASS = 'to_study-mass';
    const ADMIN = 'admin';
    const EDIT = 'edit';
    const OPEN = 'open';
    const SHOW_INFORMATIONS = 'show_informations';
    const SHOW_PROJECTS = 'show_projects';
    const SHOW_PERMISSIONS = 'show_permissions';
    const FINISHED = 'finished';
    const SUBMIT_PROJECT = 'submit_project';


    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * CallOfProjectVoter constructor.
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
            self::TO_STUDY_MASS,
            self::ADMIN,
            self::EDIT,
            self::OPEN,
            self::SHOW_INFORMATIONS,
            self::SHOW_PROJECTS,
            self::SHOW_PERMISSIONS,
            self::FINISHED,
            self::SUBMIT_PROJECT
        ])) {
            return false;
        }

        // only vote on `CallOfProject` objects
        if (!$subject instanceof CallOfProject) {
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

        /** @var CallOfProject $callOfProject */
        $callOfProject = $subject;

        switch ($attribute) {
            case self::TO_STUDY_MASS:
                return $this->canApplyToStudyMass($callOfProject, $user);
            case self::ADMIN:
                return $this->canAdmin($callOfProject, $user);
            case self::EDIT:
                return $this->canEdit($callOfProject, $user);
            case self::OPEN:
                return $this->isOpen($callOfProject);
            case self::SHOW_INFORMATIONS:
                return $this->canSeeInformations($callOfProject, $user);
            case self::SHOW_PROJECTS:
                return $this->canSeeProjects($callOfProject, $user);
            case self::SHOW_PERMISSIONS:
                return $this->canSeePermissions($callOfProject, $user);
            case self::FINISHED:
                return  $this->canFinished($callOfProject, $user);
            case self::SUBMIT_PROJECT:
                return  $this->canSubmitProject($callOfProject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param CallOfProject $callOfProject
     * @param User $user
     * @return bool
     */
    private function canApplyToStudyMass(CallOfProject $callOfProject, User $user): bool
    {
        if ($this->canAdmin($callOfProject, $user) === false) {
            return false;
        }

        if ($callOfProject->getStatus() !== CallOfProject::STATUS_CLOSED) {
            return false;
        }

        $projects = $callOfProject->getProjects()->filter(function(Project $project) {
            return $project->getStatus() === Project::STATUS_WAITING;
        });

        if ($projects->count() < 1) {
            return false;
        }

        return true;
    }

    private function canAdmin(CallOfProject $callOfProject, User $user)
    {
        $userPermissions = AbstractAclManager::getPermissions($user, $callOfProject);

        return count(array_intersect($userPermissions->toArray(), CallOfProject::ADMIN_PERMISSIONS)) > 0
            or $this->authorizationChecker->isGranted(OrganizingCenterVoter::ADMIN, $callOfProject->getOrganizingCenter());
    }

    private function isOpen(CallOfProject $callOfProject)
    {
        return $callOfProject->getStatus() === CallOfProject::STATUS_OPENED;
    }

    private function canSeeInformations(CallOfProject $callOfProject, User $user)
    {
        $userPermissions = AbstractAclManager::getPermissions($user, $callOfProject);
        $userPermissions = array_merge(
            AbstractAclManager::getPermissions($user, $callOfProject->getOrganizingCenter())->toArray(),
            $userPermissions->toArray()
        );

        return count(array_intersect($userPermissions, CallOfProject::VIEWER_PERMISSIONS)) > 0;

    }

    private function canSeeProjects(CallOfProject $callOfProject, User $user)
    {
        return $this->canSeeInformations($callOfProject, $user);
    }

    private function canSeePermissions(CallOfProject $callOfProject, User $user)
    {
        return $this->canSeeInformations($callOfProject, $user);
    }

    private function canEdit(CallOfProject $callOfProject, User $user)
    {
        $userPermissions = AbstractAclManager::getPermissions($user, $callOfProject);
        $userPermissions = array_merge(
            AbstractAclManager::getPermissions($user, $callOfProject->getOrganizingCenter())->toArray(), $userPermissions->toArray());

        return count(array_intersect($userPermissions, CallOfProject::EDIT_PERMISSIONS)) > 0;
    }

    /**
     * @param CallOfProject $callOfProject
     * @param User $user
     * @return bool
     */
    private function canFinished(CallOfProject $callOfProject, User $user)
    {
        if (!$this->canAdmin($callOfProject, $user)) {
            return false;
        }

        if (in_array($callOfProject->getStatus(), [CallOfProject::STATUS_FINISHED, CallOfProject::STATUS_ARCHIVED])) {
            return false;
        }

        return true;
    }

    /**
     * @param CallOfProject $callOfProject
     * @param User $user
     * @return bool
     */
    private function canSubmitProject(CallOfProject $callOfProject, User $user)
    {
        if($callOfProject->isMultipleDeposit() || in_array('ROLE_ADMIN',  $user->getRoles())) {
            return true;
        }

        return $callOfProject->getProjects()->filter(function (Project $project) use ($user) {
                return $project->getCreatedBy() === $user;
            })->count() == 0;
    }
}
