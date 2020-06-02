<?php


namespace App\Form\Type;


use App\Form\DataTransformer\ReportFromUserTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
            ->addModelTransformer($this->reportFromUserTransformer)
        ;
    }
}