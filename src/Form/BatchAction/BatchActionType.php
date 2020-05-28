<?php


namespace App\Form\BatchAction;

use App\EventSubscriber\BatchActionTypeSubscriber;
use App\Form\DataTransformer\BatchActionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BatchActionType extends AbstractType
{
    /**
     * @var BatchActionTransformer
     */
    private $batchActionTransformer;
    /**
     * @var BatchActionTypeSubscriber
     */
    private $batchActionTypeSubscriber;

    /**
     * BatchActionType constructor.
     * @param BatchActionTransformer $batchActionTransformer
     * @param BatchActionTypeSubscriber $batchActionTypeSubscriber
     */
    public function __construct(BatchActionTransformer $batchActionTransformer, BatchActionTypeSubscriber $batchActionTypeSubscriber)
    {
        $this->batchActionTransformer = $batchActionTransformer;
        $this->batchActionTypeSubscriber = $batchActionTypeSubscriber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('entities', ChoiceType::class, [
            'multiple' => true,
            'attr' => [
                'class' => 'd-none entities-field',
                'data-prototype' => '<option value="__VALUE__" selected="selected">__LABEL__</option>',
            ]
        ])
            ->addModelTransformer($this->batchActionTransformer->setClassName($options['class_name']))
            ->addEventSubscriber($this->batchActionTypeSubscriber)
        ;

        foreach ($options['batch_actions'] as $batchAction) {
            $builder->add($batchAction->getName(), SubmitType::class, [
                'label' => 'app.batch_action.' . $batchAction->getName(),
                'attr' => [
                    'class' => 'dropdown-item batch-action-button',
                    'data-name' => $batchAction->getName()
                ]
            ]);
        }

    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['batch_actions'] = $options['batch_actions'];

        $view->vars['attr']['id'] = 'batch-action-' . uniqid();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('batch_actions');
        $resolver->setRequired('class_name');
    }
}