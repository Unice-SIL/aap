<?php

namespace App\Manager\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineUserManager extends AbstractUserManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DoctrineUserManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(User $user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function update(User $user)
    {
        $this->em->flush();
    }

    public function delete(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }

}