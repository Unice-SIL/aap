<?php

namespace App\Manager\ProjectFormWidget;

use App\Entity\ProjectFormWidget;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineProjectFormWidgetManager extends AbstractProjectFormWidgetManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DoctrineProjectFormWidgetManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(ProjectFormWidget $projectFormWidget)
    {
        $this->em->persist($projectFormWidget);
        $this->em->flush();
    }

    public function update(ProjectFormWidget $projectFormWidget)
    {
        $this->em->flush();
    }

    public function delete(ProjectFormWidget $projectFormWidget)
    {
        $this->em->remove($projectFormWidget);
        $this->em->flush();
    }

}