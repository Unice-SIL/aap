<?php


namespace App\EventSubscriber;


use App\Entity\CallOfProject;
use App\Entity\ProjectFormLayout;
use App\Entity\User;
use App\Form\Type\InitProjectChoiceType;
use App\Manager\ProjectFormLayout\ProjectFormLayoutManagerInterface;
use App\Manager\ProjectFormWidget\ProjectFormWidgetManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class UserTypeSubscriber implements EventSubscriberInterface
{


    public static function getSubscribedEvents()
    {
        return [
                FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }


    public function preSetData(FormEvent $event)
    {
        /** @var User $user */
        $user = $event->getData();

        $form = $event->getForm();

        if (null !== $user and null === $user->getId()) {

           $form->add('plainPassword', null, [
               'label' => 'app.user.property.plainPassword.label',
               'validation_groups' => ['new'],
               'required' => false
           ]);
        }
    }
}