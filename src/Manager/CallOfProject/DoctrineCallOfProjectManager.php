<?php

namespace App\Manager\CallOfProject;

use App\Entity\CallOfProject;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCallOfProjectManager extends AbstractCallOfProjectManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DoctrineCallOfProjectManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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