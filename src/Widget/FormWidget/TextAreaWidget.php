<?php


namespace App\Widget\FormWidget;


use App\Form\Type\SummernoteType;
use App\Form\Widget\FormWidget\FormTextWidgetType;
use App\Form\Widget\Validation\TextWidgetValidationType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TextAreaWidget extends AbstractFormWidget implements FormWidgetInterface
{
    public function getFormType(): string
    {
        return FormTextWidgetType::class;
    }

    public function getSymfonyType(): string
    {
        return SummernoteType::class;
    }

    public function getDynamicConstraintsType(): ?string
    {
        return TextWidgetValidationType::class;
    }

    public function getOptions(): array
    {
        return array_merge_recursive(parent::getOptions(), [
            'attr' => [
                'rows' => 4,
            ]
        ]);
    }

    public function getPosition(): int
    {
        return 2;
    }


}