<?php

namespace App\Manager\ProjectFormLayout;

use App\Entity\ProjectFormLayout;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineProjectFormLayoutManager extends AbstractProjectFormLayoutManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DoctrineProjectFormLayoutManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(ProjectFormLayout $projectFormLayout)
    {
        $this->em->persist($projectFormLayout);
        $this->em->flush();
    }

    public function update(ProjectFormLayout $projectFormLayout)
    {
        $this->em->flush();
    }

    public function delete(ProjectFormLayout $projectFormLayout)
    {
        $this->em->remove($projectFormLayout);
        $this->em->flush();
    }

}