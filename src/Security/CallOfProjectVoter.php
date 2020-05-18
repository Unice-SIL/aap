<?php

namespace App\Security;

use App\Entity\Acl;
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

    const EDIT = 'edit';

    const OPEN = 'open';
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
                self::EDIT,
                self::OPEN,
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

        /** @var CallOfProject $callOfProject */
        $callOfProject = $subject;

        switch ($attribute) {
            case self::TO_STUDY_MASS:
                return $this->canApplyToStudyMass($callOfProject, $user);
            case self::EDIT:
                return $this->canEdit($callOfProject, $user);
            case self::OPEN:
                return $this->isOpen($callOfProject);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canApplyToStudyMass(CallOfProject $callOfProject, User $user)
    {
        $projectStatuses = $callOfProject->getProjects()->map(function($project) {
            return $project->getStatus();
        });

        if ($callOfProject->getStatus() !== CallOfProject::STATUS_REVIEW) {
            return false;
        }

        if (!in_array(Project::STATUS_WAITING, $projectStatuses->toArray())) {
            return false;
        }

        if (!$this->canEdit($callOfProject, $user)) {
            return false;
        }

        return true;
    }

    private function canEdit(CallOfProject $callOfProject, User $user)
    {
        $userPermissions = AbstractAclManager::getPermissions($user, $callOfProject);

        return count(array_intersect($userPermissions->toArray(), CallOfProject::EDITOR_PERMISSIONS)) > 0
            or $this->authorizationChecker->isGranted(OrganizingCenterVoter::EDIT, $callOfProject->getOrganizingCenter());
    }

    private function isOpen(CallOfProject $callOfProject)
    {
        return $callOfProject->getStatus() === CallOfProject::STATUS_OPENED;
    }
}
