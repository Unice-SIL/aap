<?php

namespace App\Security;

use App\Entity\ProjectContent;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectContentVoter extends Voter
{

    const DOWNLOAD = 'download';

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * CallOfProjectContentVoter constructor.
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DOWNLOAD])) {
            return false;
        }

        // only vote on `ProjectContent` objects
        if (!$subject instanceof ProjectContent) {
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

        /** @var ProjectContent $projectContent */
        $projectContent = $subject;

        switch ($attribute) {
            case self::DOWNLOAD:
                return $this->canDownload($projectContent, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canDownload(ProjectContent $projectContent, User $user)
    {
        $project = $projectContent->getProject();
        if ($project->getCreatedBy() === $user) {
            return true;
        }

        //todo: if rapporteur

        if ($this->authorizationChecker->isGranted(CallOfProjectVoter::SHOW_INFORMATIONS, $project->getCallOfProject())) {
            return true;
        }

        return false;
    }

}
