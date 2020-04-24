<?php

namespace App\Manager\ProjectContent;

use App\Entity\ProjectContent;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineProjectContentManager extends AbstractProjectContentManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DoctrineProjectContentManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(ProjectContent $projectContent)
    {
        $this->em->persist($projectContent);
        $this->em->flush();
    }

    public function update(ProjectContent $projectContent)
    {
        $this->em->flush();
    }

    public function delete(ProjectContent $projectContent)
    {
        $this->em->remove($projectContent);
        $this->em->flush();
    }

}