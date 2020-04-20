<?php


namespace App\Widget\FormWidget;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

abstract class AbstractChoiceWidget extends AbstractFormWidget implements FormWidgetInterface
{
    /** @var array  */
    private $choices = [];

    public function getType(): string
    {
        return self::TYPE_TEXT;
    }

    public function getSymfonyType(): string
    {
        return ChoiceType::class;
    }

    public function getDataTransformer(): DataTransformerInterface
    {
        // TODO: Implement getDataTransformer() method.
    }

    /**
     * @return array
     */
    public function getChoices(): array
    {
        return $this->choices;
    }

    /**
     * @param array $choices
     */
    public function setChoices(array $choices): void
    {
        $this->choices = $choices;
    }

    public function addChoice(string $choice): self
    {
        $this->choices[] = $choice;

        return $this;
    }

    protected function configureOptions(): void
    {
        parent::configureOptions();
        $this->addOptions([
            'choices' => array_flip($this->getChoices())
        ]);
    }

    public function getTemplate(): string
    {
        return 'partial/widget/form_widget/_choice_widget.html.twig';
    }
}