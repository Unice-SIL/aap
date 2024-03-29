<?php


namespace App\Widget\FormWidget;


use App\Form\Widget\FormWidget\FormTextWidgetType;
use App\Form\Widget\Validation\TextWidgetValidationType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TextWidget extends AbstractFormWidget implements FormWidgetInterface
{

    public function getFormType(): string
    {
        return FormTextWidgetType::class;
    }

    public function getSymfonyType(): string
    {
        return TextType::class;
    }

    public function getDynamicConstraintsType(): ?string
    {
        return TextWidgetValidationType::class;
    }

    public function getPosition(): int
    {
        return 1;
    }


}