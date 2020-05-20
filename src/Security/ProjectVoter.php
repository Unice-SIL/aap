<?php

namespace App\Security;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectVoter extends Voter
{

    const EDIT = 'edit';
    const SHOW = 'show';

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
        if (!in_array($attribute, [self::EDIT, self::SHOW])) {
            return false;
        }

        // only vote on `Project` objects
        if (!$subject instanceof Project) {
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


        /** @var Project $project */
        $project = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($project, $user);
            case self::SHOW:
                return $this->canSee($project, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Project $project, User $user)
    {
        if ($project->getCreatedBy() === $user and $project->getStatus() === Project::STATUS_WAITING) {
            return true;
        }

        if ($this->authorizationChecker->isGranted(CallOfProjectVoter::EDIT, $project->getCallOfProject())) {
            return true;
        }

        return false;
    }

    private function canSee(Project $project, User $user)
    {
        if ($project->getCreatedBy() === $user) {
            return true;
        }

        //todo: if raporteur

        if ($this->authorizationChecker->isGranted(CallOfProjectVoter::SHOW_INFORMATIONS, $project->getCallOfProject())) {
            return true;
        }

        return false;
    }
}
