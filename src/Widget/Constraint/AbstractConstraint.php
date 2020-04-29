<?php


namespace App\Widget\Constraint;


abstract class AbstractConstraint implements WidgetConstraintInterface
{
    /**
     * @var bool
     */
    private $active = false;

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    abstract public function getSymfonyConstraint();
}