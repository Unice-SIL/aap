<?php

namespace App\Manager\Report;

use App\Entity\Report;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineReportManager extends AbstractReportManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DoctrineReportManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(Report $report)
    {
        $this->em->persist($report);
        $this->em->flush();
    }

    public function update(Report $report)
    {
        $this->em->flush();
    }

    public function delete(Report $report)
    {
        $this->em->remove($report);
        $this->em->flush();
    }

}