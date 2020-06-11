<?php


namespace App\Manager\Invitation;


use App\Entity\Invitation;
use App\Entity\User;

abstract class AbstractInvitationManager implements InvitationManagerInterface
{

    public function create(User $user): Invitation
    {

        $invitation = new Invitation();
        $invitation->setName('Invitation envoyée à ' . $user->getEmail());
        $invitation->setUserEmail($user->getEmail());
        $invitation->setUserFirstname($user->getFirstname());
        $invitation->setUserLastname($user->getLastname());

        return $invitation;
    }

    public abstract function save(Invitation $invitation);

    public abstract function update(Invitation $invitation);

    public abstract function delete(Invitation $invitation);


}