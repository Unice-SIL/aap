<?php

namespace App\Constant;

use App\Entity\CallOfProject;

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
        'draft' => 'warning'
    ];
}