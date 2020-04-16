<?php

namespace App\EventSubscriber\Form;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CallOfProjectInformationTypeSubscriber implements EventSubscriberInterface
{
    public function preSetData(FormEvent $event)
    {
        $callOfProject = $event->getData();
        $form = $event->getForm();

        if (!$callOfProject || null === $callOfProject->getId()) {
            $form->add('name');
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }
}
