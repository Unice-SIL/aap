<?php


namespace App\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class BatchActionTypeSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit'
        ];
    }
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $entitiesField = $form->get('entities');
        $type = get_class($entitiesField->getConfig()->getType()->getInnerType());

        $options = $entitiesField->getConfig()->getOptions();
        $options['choices'] = $event->getData()['entities'];

        $form->add('entities', $type, $options);

    }
}