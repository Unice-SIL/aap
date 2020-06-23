<?php


namespace App\Form\ProjectFormLayout;

use App\Entity\CallOfProject;
use App\Form\Type\CallOfProjectSelect2EntityType;
use App\Form\Type\ProjectFormWidgetSelect2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class ImportWidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('callOfProject', CallOfProjectSelect2EntityType::class, [
                'remote_route' => 'app.call_of_project.list_all_select_2',
                'label' => 'app.call_of_project_label',
                'required' => false,
            ])
        ;


        $formModifier = function (FormInterface $form, CallOfProject $callOfProject = null) {
            $routeParams = null === $callOfProject ? [] : ['call_of_project_id' => $callOfProject->getId()];

            $form->add('projectFormWidget', ProjectFormWidgetSelect2EntityType::class, [
                'mapped' => false,
                'remote_route' => 'app.project_form_widget.list_all_select_2',
                'remote_params' => $routeParams,
                'label' => 'app.project_form_widget_label',
                'required' => true
            ])
            ;
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {

                $formModifier($event->getForm(), null);
            }
        );

        $builder->get('callOfProject')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                /** @var CallOfProject $callOfProject */
                $callOfProject = $event->getForm()->getData();

                $formModifier($event->getForm()->getParent(), $callOfProject);
            }
        );

    }
}