<?php

namespace App\Security;

use App\Entity\Invitation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class InvitationVoter extends Voter
{

    const MANAGE = 'manage';

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * CallOfInvitationVoter constructor.
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
                self::MANAGE,
            ])) {
            return false;
        }

        // only vote on `Invitation` objects
        if (!$subject instanceof Invitation) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var Invitation $invitation */
        $invitation = $subject;

        switch ($attribute) {
            case self::MANAGE:
                return $this->canManage($user, $invitation);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canManage(User $user, Invitation $invitation)
    {
        return $invitation->getCreatedBy() === $user;
    }

}
