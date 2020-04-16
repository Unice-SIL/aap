<?php


namespace App\Widget\FormWidget;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ChoiceWidget extends FormWidgetAbstract implements FormWidgetInterface
{
    const NAME = 'form_choice_widget';

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
        return self::TYPE_TEXT;
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
        return ChoiceType::class;
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
        return 'partial/widget/form_widget/_form_choice_widget.html.twig';
    }
}