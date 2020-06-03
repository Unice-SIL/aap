<?php

namespace App\Security;

use App\Entity\Report;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ReportVoter extends Voter
{

    const EDIT = 'edit';
    const SHOW = 'show';

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * CallOfReportVoter constructor.
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

        // only vote on `Report` objects
        if (!$subject instanceof Report) {
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


        /** @var Report $report */
        $report = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($report, $user);
            case self::SHOW:
                return $this->canSee($report, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Report $report, User $user)
    {
        if ($report->getReporter() === $user and $report->getStatus() !== Report::STATUS_FINISHED) {
            return true;
        }

        return false;
    }

    private function canSee(Report $report, User $user)
    {
        if ($report->getReporter() === $user) {
            return true;
        }
        return false;
    }
}
