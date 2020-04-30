<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValueRequiredIfActiveValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
        if (
            $entity->isActive()
            and $entity->getValue() === null
        ) {
            $this->context->buildViolation($constraint->message)
                ->atPath('value')
                ->addViolation();
        }
    }
}
