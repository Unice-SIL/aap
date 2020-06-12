<?php

namespace App\Manager\Invitation;


use App\Entity\Invitation;
use App\Entity\User;

interface InvitationManagerInterface
{
    public function create(User $user): Invitation;

    public function save(Invitation $invitation);

    public function update(Invitation $invitation);

    public function delete(Invitation $invitation);
}