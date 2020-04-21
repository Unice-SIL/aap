<?php


namespace App\Widget\FormWidget;


use App\Widget\AbstractWidget;
use Symfony\Component\Form\DataTransformerInterface;

abstract class AbstractFormWidget extends AbstractWidget
{
    /** @var string|null */
    protected $label;

    /** @var bool */
    protected $required = false;

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
     * @return bool
     */
    public function getRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        $this->configureOptions();
        return $this->options;

    }

    protected function configureOptions(): void
    {
        $this->addOptions(
            [
                'label' => $this->getLabel(),
                'required' => $this->getRequired(),
            ]
        );
    }

    protected function addOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
    }

    public function getDataTransformer(): DataTransformerInterface
    {
    }
}