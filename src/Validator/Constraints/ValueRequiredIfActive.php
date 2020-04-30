<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValueRequiredIfActive extends Constraint
{
    public $message = 'app.widget.errors.validation.value_required_if_active';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}