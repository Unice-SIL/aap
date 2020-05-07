<?php


namespace App\Widget\FormWidget;


use App\Form\Widget\FormWidget\FormChoiceWidgetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

abstract class AbstractChoiceWidget extends AbstractFormWidget implements FormWidgetInterface
{
    /** @var array  */
    private $choices = [];

    public function getFormType(): string
    {
        return FormChoiceWidgetType::class;
    }

    public function getSymfonyType(): string
    {
        return ChoiceType::class;
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

    public function getOptions(): array
    {
        return array_merge_recursive(parent::getOptions(), [
            'choices' => array_flip($this->getChoices()),
        ]);
    }

    public function getTemplate(): string
    {
        return 'partial/widget/form_widget/_choice_widget.html.twig';
    }

    public function transformData($value, array $options = [])
    {
        return unserialize($value);
    }


    public function reverseTransformData($value, array $options = [])
    {
        return serialize($value);
    }


}