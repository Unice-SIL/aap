<?php


namespace App\Validator\Entities;


use App\Entity\Report;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class FileNotBlankValidator
{
    /**
     * @param Report $report
     * @param ExecutionContextInterface $context
     */
    public static function validate(Report $report, ExecutionContextInterface $context)
    {
        if ($report->getReport()->getName() === null && $report->getReportFile() === null) {
            $context->buildViolation('app.file.cannot_be_blank')
                ->atPath('reportFile')
                ->addViolation();
        }
    }
}