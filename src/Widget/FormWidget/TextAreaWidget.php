<?php


namespace App\Widget\FormWidget;


use App\Form\Widget\FormWidget\FormTextWidgetType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TextAreaWidget extends AbstractFormWidget implements FormWidgetInterface
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
        return TextareaType::class;
    }

    protected function configureOptions(): void
    {
        parent::configureOptions();

        $this->addOptions([
            'attr' => [
                'rows' => 4
            ]
        ]);
    }


}