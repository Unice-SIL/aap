<?php

namespace App\Manager\Project;

use App\Entity\Project;
use App\Manager\ProjectContent\ProjectContentManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineProjectManager extends AbstractProjectManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DoctrineProjectManager constructor.
     * @param EntityManagerInterface $em
     * @param ProjectContentManagerInterface $projectContentManager
     */
    public function __construct(EntityManagerInterface $em, ProjectContentManagerInterface $projectContentManager)
    {
        parent::__construct($projectContentManager);
        $this->em = $em;
    }

    public function save(Project $project)
    {
        $this->em->persist($project);
        $this->em->flush();
    }

    public function update(Project $project)
    {
        $this->em->flush();
    }

    public function delete(Project $project)
    {
        $this->em->remove($project);
        $this->em->flush();
    }

}