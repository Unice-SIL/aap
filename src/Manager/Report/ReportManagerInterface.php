<?php

namespace App\Manager\Report;


use App\Entity\Report;

interface ReportManagerInterface
{
    public function create(): Report;

    public function save(Report $report);

    public function update(Report $report);

    public function delete(Report $report);
}