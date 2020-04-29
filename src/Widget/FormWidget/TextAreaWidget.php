<?php


namespace App\Widget\FormWidget;


use App\Form\Widget\FormWidget\FormTextWidgetType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TextAreaWidget extends AbstractFormWidget implements FormWidgetInterface
{
    public function getFormType(): string
    {
        return FormTextWidgetType::class;
    }

    public function getSymfonyType(): string
    {
        return TextareaType::class;
    }

    protected function configureOptions(): array
    {
        $options = parent::configureOptions();

        $attr = $options['attr'] ?? [];
        $attr['rows'] = 4;
        $this->addOptions(['attr' => $attr]);

        return $this->options;
    }


}