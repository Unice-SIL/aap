<?php


namespace App\Manager\Report;


use App\Entity\Report;

abstract class AbstractReportManager implements ReportManagerInterface
{

    public function create(): Report
    {
        return new Report();
    }

    public abstract function save(Report $report);

    public abstract function update(Report $report);

    public abstract function delete(Report $report);


}