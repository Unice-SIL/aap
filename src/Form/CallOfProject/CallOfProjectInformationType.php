<?php

namespace App\Form\CallOfProject;

use App\Entity\CallOfProject;
use App\EventSubscriber\CallOfProjectInformationTypeSubscriber;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\SummernoteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;

class CallOfProjectInformationType extends AbstractType
{
    /**
     * @var CallOfProjectInformationTypeSubscriber
     */
    private $callOfProjectInformationTypeSubscriber;

    /**
     * CallOfProjectInformationType constructor.
     * @param CallOfProjectInformationTypeSubscriber $callOfProjectInformationTypeSubscriber
     */
    public function __construct(CallOfProjectInformationTypeSubscriber $callOfProjectInformationTypeSubscriber)
    {

        $this->callOfProjectInformationTypeSubscriber = $callOfProjectInformationTypeSubscriber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'app.call_of_project.property.name.label',
                'attr' => [
                    'placeholder' => 'app.call_of_project.property.name.placeholder'
                ],
            ])
            ->add('description', SummernoteType::class, [
                'required' => false,
                'label' => 'app.call_of_project.property.description.label',
                'attr' => [
                    'rows' => 8,
                    'placeholder' => 'app.call_of_project.property.description.placeholder'
                ]
            ])
            ->add('public', CheckboxType::class, [
                'required' => false,
                'label' => 'app.call_of_project.property.public.label',
                'label_attr' => ['class' => 'switch-custom']
            ])
            ->add('startDate', DateTimePickerType::class, [
                'label' => 'app.call_of_project.property.start_date.label',
                'attr' => [
                    'data-linked-target' => $uniqid = uniqid(),
                ]
            ])
            ->add('endDate', DateTimePickerType::class, [
                'label' => 'app.call_of_project.property.end_date.label',
                'attr' => [
                    'data-linked-id' => $uniqid
                ]
            ])
            ->add('publicationDate', DateTimePickerType::class, [
                'label' => 'app.call_of_project.property.publication_date.label',
                'required' => false

            ])
            ->addEventSubscriber($this->callOfProjectInformationTypeSubscriber)
            ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CallOfProject::class,
        ]);

    }
}
