<?php


namespace App\Widget\Constraint;


use Symfony\Component\Validator\Constraints\Email;

class EmailConstraint extends AbstractConstraint
{
    
    public function getSymfonyConstraint()
    {
        return new Email();
    }

}