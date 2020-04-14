<?php

namespace App\Form\CallOfProject;

use App\Entity\CallOfProject;
use App\Entity\ProjectFormLayout;
use App\Form\ProjectFormLayout\ProjectFormLayoutEmbeddedType;
use App\Repository\ProjectFormLayoutRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CallOfProjectInformationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', null, [
                'attr' => [
                    'rows' => 8
                ],
                'label' => 'app.call_of_project.property.description'
            ])
            /*->add('fromTemplate', CheckboxType::class, [
                'false_values' => ['false', '0', null],
                'required' => false,
                'mapped' => false
            ]);*/
        ;

        /**
         * ========================== Listener=========================
         */

        /**
         * @param FormInterface $form
         * @param bool $isFromTemplate
         */
//        $formModifier = function (FormInterface $form, bool $isFromTemplate) use ($builder) {
//
//            /** @var CallOfProject $callOfProject */
//            $callOfProject = $builder->getData();
//
//            // If new entity
//            if (!$callOfProject || null === $callOfProject->getId()) {
//
//                $type = EntityType::class;
//                $projectFormLayoutOptions = [
//                    'query_builder' => function (ProjectFormLayoutRepository $projectFormLayoutRepository) {
//                        return $projectFormLayoutRepository->getIsTemplateBuilder();
//                    },
//                    'class' => ProjectFormLayout::class
//                ];
//
//                if (!$isFromTemplate) {
//                    $type = ProjectFormLayoutEmbeddedType::class;
//                    $projectFormLayoutOptions = [];
//                }
//
//                $form->add('projectFormLayout', $type, array_merge(
//                    [
//                        'label' => false
//                    ],
//                    $projectFormLayoutOptions
//                ));
//
//            } else { //If update entity
//                $form->remove('fromTemplate');
//            }
//        };
//
//        $builder->addEventListener(
//            FormEvents::PRE_SET_DATA,
//            function (FormEvent $event) use ($formModifier) {
//
//                $formModifier($event->getForm(), $event->getForm()->get('fromTemplate')->getData());
//            }
//        );
//
//        $builder->get('fromTemplate')->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function (FormEvent $event) use ($formModifier) {
//                // It's important here to fetch $event->getForm()->getData(), as
//                // $event->getData() will get you the client data (that is, the ID)
//                $isFromTemplate = $event->getForm()->getData();
//
//                // since we've added the listener to the child, we'll have to pass on
//                // the parent to the callback functions!
//                $formModifier($event->getForm()->getParent(), $isFromTemplate);
//            }
//        );

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
