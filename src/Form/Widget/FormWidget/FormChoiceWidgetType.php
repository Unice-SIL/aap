<?php


namespace App\Form\Widget\FormWidget;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class FormChoiceWidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('choices', CollectionType::class, [
            'entry_type' => TextType::class,
            'label_format' => 'Option',
            'label' => 'Choices',
            'allow_add' => true,
            'allow_delete' => true
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $widget = $event->getData();

            if (count($widget->getChoices()) === 0) {
                $widget->addChoice('');
            }

        });
    }

    public function getParent()
    {
        return FormWidgetType::class;
    }

}