<?php


namespace App\Widget\FormWidget;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class TextWidget implements FormWidgetInterface
{
    const NAME = 'form_text_widget';

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * TextWidget constructor.
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }


    public function getType(): string
    {
        self::TYPE_TEXT;
    }

    public function getForm(): FormInterface
    {
        return $this->formFactory->createBuilder()
            ->add('label')
            ->getForm()
            ;

    }

    public function getSymfonyType(): string
    {
        return TextType::class;
    }

    public function getDataTransformer(): DataTransformerInterface
    {
        // TODO: Implement getDataTransformer() method.
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTemplate(): string
    {
        return 'partial/widget/form_widget/_form_text_widget.html.twig';
    }
}