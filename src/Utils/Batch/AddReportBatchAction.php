<?php


namespace App\Utils\Batch;


use App\Entity\Project;
use App\Entity\Report;
use App\Entity\User;
use App\Form\BatchAction\AddReporterBatchActionType;
use Symfony\Component\Form\FormInterface;

class AddReportBatchAction extends AbstractBatchAction
{

    public function getName(): string
    {
        return 'add_reporter';
    }

    public function supports($entity): bool
    {
        $authorizedClasses = [Project::class];

        foreach ($authorizedClasses as $authorizedClass) {
            if ($entity instanceof $authorizedClass) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Project $entity
     * @param FormInterface $form
     * @throws \Exception
     */
    protected function processOnEntity($entity, FormInterface $form)
    {
        if (get_class($form->getConfig()->getType()->getInnerType()) !== $this->getFormClassName()) {
            throw new \Exception('The form should be a instance of ' . $this->getFormClassName());
        }

        $reports = $form->getData()['reports'];

        $currentReporters = $entity->getReports()->map(function ($report) {
            return $report->getReporter();
        });

        foreach ($reports as $report) {
            if (!$currentReporters->contains($report->getReporter())) {
                $report = clone $report;
                $entity->addReport($report);
            }
        }

    }

    public function getFormClassName(): string
    {
        return AddReporterBatchActionType::class;
    }
}