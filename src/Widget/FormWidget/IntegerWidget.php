<?php


namespace App\Widget\FormWidget;


use App\Form\Widget\FormWidget\FormIntegerWidgetType;
use App\Form\Widget\Validation\IntegerWidgetValidationType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class IntegerWidget extends AbstractFormWidget implements FormWidgetInterface
{

    public function getFormType(): string
    {
        return FormIntegerWidgetType::class;
    }

    public function getSymfonyType(): string
    {
        return IntegerType::class;
    }

    public function getDynamicConstraintsType(): ?string
    {
        return IntegerWidgetValidationType::class;
    }

    public function getPosition(): int
    {
        return 3;
    }
}