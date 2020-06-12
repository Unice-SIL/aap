<?php

namespace App\Manager\CallOfProject;

use App\Entity\CallOfProject;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCallOfProjectManager extends AbstractCallOfProjectManager
{

    /**
     * DoctrineCallOfProjectManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
    }

    public function save(CallOfProject $callOfProject)
    {
        $this->em->persist($callOfProject);
        $this->em->flush();
    }

    public function update(CallOfProject $callOfProject)
    {
        $this->em->flush();
    }

    public function delete(CallOfProject $callOfProject)
    {
        $this->em->remove($callOfProject);
        $this->em->flush();
    }

}