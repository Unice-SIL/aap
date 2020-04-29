<?php


namespace App\Widget\Constraint;


use Symfony\Component\Validator\Constraints\Url;

class UrlConstraint extends AbstractConstraint
{
    
    public function getSymfonyConstraint()
    {
        return new Url();
    }

}