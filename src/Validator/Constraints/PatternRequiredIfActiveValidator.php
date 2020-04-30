<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PatternRequiredIfActiveValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
        if (
            $entity->isActive()
            and $entity->getPattern() === null
        ) {
            $this->context->buildViolation($constraint->message)
                ->atPath('pattern')
                ->addViolation();
        }
    }
}
