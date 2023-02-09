<?php

namespace App\Security;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter
{

    const DELETE = 'delete';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DELETE])) {
            return false;
        }

        // only vote on `Comment` objects
        if (!$subject instanceof Comment) {
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

        /** @var Comment $comment */
        $comment = $subject;

        switch ($attribute) {
            case self::DELETE:
                return $this->canDelete($comment, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canDelete(Comment $comment, User $user)
    {
        if ($comment->getUser() === $user) {
            return true;
        }

        return false;
    }
}
