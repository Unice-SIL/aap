<?php


namespace App\Widget\FormWidget;


use App\Form\Widget\FormWidget\FormTextWidgetType;
use App\Widget\WidgetInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;

class TextWidget extends FormWidgetAbstract implements FormWidgetInterface
{
    public function getType(): string
    {
        self::TYPE_TEXT;
    }

    public function getFormType(): string
    {
        return FormTextWidgetType::class;

    }

    public function getSymfonyType(): string
    {
        return TextType::class;
    }

    public function getDataTransformer(): DataTransformerInterface
    {
        // TODO: Implement getDataTransformer() method.
    }

}