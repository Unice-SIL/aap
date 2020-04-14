<?php


namespace App\Widget\FormWidget;


use App\Widget\AbstractWidget;

abstract class FormWidgetAbstract extends AbstractWidget
{
    /** @var string|null */
    protected $label;

    /** @var string|null */
    protected $required;

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     */
    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return string|null
     */
    public function getRequired(): ?string
    {
        return $this->required;
    }

    /**
     * @param string|null $required
     */
    public function setRequired(?string $required): void
    {
        $this->required = $required;
    }


}