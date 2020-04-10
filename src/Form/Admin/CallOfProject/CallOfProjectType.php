<?php

namespace App\Form\Admin\CallOfProject;

use App\Entity\CallOfProject;
use App\Form\Admin\ProjectFormLayout\ProjectFormLayoutEmbeddedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallOfProjectType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('fromTemplate', CheckboxType::class, [
                'false_values' => ['false', '0', null],
                'required' => false
            ]);
        ;

        /**
         * ========================== Listener=========================
         */

        /**
         * @param FormInterface $form
         * @param bool $isFromTemplate
         */
        $formModifier = function (FormInterface $form, bool $isFromTemplate) use ($builder) {

            /** @var CallOfProject $callOfProject */
            $callOfProject = $builder->getData();

            if (!$callOfProject || null === $callOfProject->getId()) {

                if ($isFromTemplate) {
                    $type = null;
                } else {
                    $type = ProjectFormLayoutEmbeddedType::class;
                }
                    $form->add('projectFormLayout', $type, [
                        'label' => false
                    ] );
            } else {
                $form->remove('fromTemplate');
            }
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {

                /** @var CallOfProject $callOfProject */
                $callOfProject = $event->getData();

                $formModifier($event->getForm(), $callOfProject->isFromTemplate());
            }
        );

        $builder->get('fromTemplate')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $isFromTemplate = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $isFromTemplate);
            }
        );

        /**
         * ========================== End Listener=========================
         */
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CallOfProject::class,
        ]);
    }
}
