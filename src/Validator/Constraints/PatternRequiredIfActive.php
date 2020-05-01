<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PatternRequiredIfActive extends Constraint
{
    public $message = 'app.widget.errors.validation.regex_required_if_active';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}