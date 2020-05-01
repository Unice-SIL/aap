<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MinOrMaxIfActive extends Constraint
{
    public $message = 'app.widget.errors.validation.min_or_max_if_active';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}