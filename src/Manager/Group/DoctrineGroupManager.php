<?php

namespace App\Manager\Group;

use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineGroupManager extends AbstractGroupManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DoctrineGroupManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Group $group)
    {
        $this->em->persist($group);
        $this->em->flush();
    }

    public function update(Group $group)
    {
        $this->em->flush();
    }

    public function delete(Group $group)
    {
        $this->em->remove($group);
        $this->em->flush();
    }

}