<?php


namespace App\Form\BatchAction;


use App\Entity\Report;
use App\Form\Type\ReportFromUserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class AddReporterBatchActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reports', ReportFromUserType::class, [
                'label' => false,
                'constraints' => [new Count([
                    'min' => 1,
                    'groups' => ['batch_Action']
                ])],
                'notification_type' => Report::NOTIFY_REPORTS
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'app.action.save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['batch_Action'],
        ]);
    }
}