<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MinOrMaxIfActiveValidator extends ConstraintValidator
{
    public function validate($entity, Constraint $constraint)
    {
        if (
            $entity->isActive()
            and $entity->getMin() === null
            and $entity->getMax() === null
        ) {
            $this->context->buildViolation($constraint->message)
                ->atPath('min')
                ->addViolation();
        }
    }
}
