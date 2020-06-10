<?php


namespace App\Form\Type;


use App\Form\DataTransformer\ReportFromUserTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportFromUserType extends AbstractType
{
    /**
     * @var ReportFromUserTransformer
     */
    private $reportFromUserTransformer;

    /**
     * ReportFromUserType constructor.
     * @param ReportFromUserTransformer $reportFromUserTransformer
     */
    public function __construct(ReportFromUserTransformer $reportFromUserTransformer)
    {
        $this->reportFromUserTransformer = $reportFromUserTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('users', UserSelect2EntityType::class, [
                'label' => 'app.user.label'
            ])
            ->add('deadline', DateTimePickerType::class)
            ->add('notifyReporters', CheckboxType::class, [
                'label' => 'app.report.notify_reporters_by_mail',
                'required' => false
            ])
            ->add('deadline', DateTimePickerType::class, [
                'label' => 'app.report.property.dead_line.label'
            ])
            ->addModelTransformer($this->reportFromUserTransformer)
        ;

        $builder->get('notifyReporters')->addModelTransformer(new CallbackTransformer(
            function ($notifyReporters) {
                return false;
            },
            function ($notifyReporters) use ($options) {
                if ($notifyReporters) {
                    return $options['notification_type'];
                }

                return null;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('notification_type');
    }
}