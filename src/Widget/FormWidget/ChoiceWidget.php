<?php


namespace App\Widget\FormWidget;


use App\Form\Widget\FormWidget\FormChoiceWidgetType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChoiceWidget extends FormWidgetAbstract implements FormWidgetInterface
{
    public function getType(): string
    {
        return self::TYPE_TEXT;
    }

    public function getFormType(): string
    {
        return FormChoiceWidgetType::class;
    }

    public function getSymfonyType(): string
    {
        return ChoiceType::class;
    }

    public function getDataTransformer(): DataTransformerInterface
    {
        // TODO: Implement getDataTransformer() method.
    }

}