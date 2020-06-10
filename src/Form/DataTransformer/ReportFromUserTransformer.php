<?php


namespace App\Form\DataTransformer;

use App\Entity\Report;
use App\Event\NewReportEvent;
use App\Manager\Report\ReportManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ReportFromUserTransformer implements DataTransformerInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ReportManagerInterface
     */
    private $reportManager;

    /**
     * BaseAclTransformer constructor.
     * @param EntityManagerInterface $em
     * @param ReportManagerInterface $reportManager
     */
    public function __construct(EntityManagerInterface $em, ReportManagerInterface $reportManager)
    {
        $this->em = $em;
        $this->reportManager = $reportManager;
    }

    public function transform($value)
    {
        return [
            'old_reports' => $value ? $value->toArray() : []
        ];
    }

    public function reverseTransform($value)
    {

        /** @var array $reports */
        $reports = $value['old_reports'];

        if (!isset($value['users'])) {
            return $reports;
        }

        $reporters = array_map(function ($report) {
                return $report->getReporter();
            }, $reports);

        foreach ($value['users'] as $user) {

            if (in_array($user, $reporters)) {
                continue;
            }

            $report = $this->reportManager->create();
            $report->setReporter($user)
                ->setDeadline($value['deadline']);
            $reports[] = $report;

        }

        return $reports;
    }
}