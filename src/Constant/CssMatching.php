<?php

namespace App\Constant;

use App\Entity\CallOfProject;
use App\Entity\Project;
use App\Entity\Report;

class CssMatching
{
    const BOOTSTRAP_TO_TOASTR = [
      'danger' => 'error'
    ];

    const CALL_OF_PROJECT_STATUS_TO_BOOTSTRAP = [
        CallOfProject::STATUS_CLOSED => 'danger',
        CallOfProject::STATUS_OPENED => 'success',
        CallOfProject::STATUS_REVIEW => 'info',
        CallOfProject::STATUS_ARCHIVED => 'warning',
    ];

    const PROJECT_STATUS_TO_BOOTSTRAP = [
        Project::STATUS_WAITING => 'info',
        Project::STATUS_STUDYING => 'warning',
        Project::STATUS_REFUSED => 'danger',
        Project::STATUS_VALIDATED => 'success',
    ];

    const REPORT_STATUS_TO_BOOTSTRAP = [
        Report::STATUS_TO_COMPLETE => 'warning',
        Report::STATUS_COMPLETE => 'info',
        Report::STATUS_FINISHED => 'danger',
    ];

    const PROJECT_FORM_WIDGET_IS_ACTIVE_BOOTSTRAP = [
      true => 'danger',
      false => 'success',
    ];
}