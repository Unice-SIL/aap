<?php


namespace App\Widget\FormWidget;


use App\Widget\AbstractWidget;

abstract class FormWidgetAbstract extends AbstractWidget
{
    /** @var string|null */
    protected $label;

    /** @var string|null */
    protected $required;

    /** @var array */
    protected $options = [];

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

    /**
     * @return array
     */
    public function getOptions(): array
    {
        $this->configureOptions();

        return array_merge([
            'label' => $this->getLabel(),
            'required' => $this->getRequired(),
        ], $this->options);
    }

    /**
     * @param array $options
     * @return FormWidgetAbstract
     */
    protected function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    protected function configureOptions(): void
    {
    }

}