<?php

namespace App\Manager\Invitation;

use App\Entity\Invitation;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineInvitationManager extends AbstractInvitationManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DoctrineInvitationManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Invitation $invitation)
    {
        $this->em->persist($invitation);
        $this->em->flush();
    }

    public function update(Invitation $invitation)
    {
        $this->em->flush();
    }

    public function delete(Invitation $invitation)
    {
        $this->em->remove($invitation);
        $this->em->flush();
    }

}