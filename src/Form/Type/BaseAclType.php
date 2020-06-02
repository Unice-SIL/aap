<?php


namespace App\Form\Type;


use App\Entity\Acl;
use App\Form\DataTransformer\BaseAclTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseAclType extends AbstractType
{
    /**
     * @var BaseAclTransformer
     */
    private $baseAclTransformer;

    /**
     * BaseAclType constructor.
     * @param BaseAclTransformer $baseAclTransformer
     */
    public function __construct(BaseAclTransformer $baseAclTransformer)
    {
        $this->baseAclTransformer = $baseAclTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach (Acl::PERMISSION_BASES as $base) {
            $builder
                ->add($base, Select2Type::class, [
                    'label' => 'app.acl.constant.' . $base,
                    'multiple' => true,
                ]);

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($base) {

                $form = $event->getForm();
                $data = $event->getData();
                $baseFiled = $form->get($base);
                $type = get_class($baseFiled->getConfig()->getType()->getInnerType());

                $options = $baseFiled->getConfig()->getOptions();

                foreach ($data as $acl) {
                    if ($base === $acl->getPermission()) {
                        $options['choices'][$acl->getName()] =  $acl->getEntity()->getId();
                        $options['data'][$acl->getName()] =  $acl->getEntity()->getId();
                    }
                }

                $form->add($base, $type, $options);

            });
            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($base) {
                $form = $event->getForm();
                $baseFiled = $form->get($base);
                $type = get_class($baseFiled->getConfig()->getType()->getInnerType());

                $options = $baseFiled->getConfig()->getOptions();

                if (isset($event->getData()[$base])) {
                    $options['choices'] = $event->getData()[$base];

                    $form->add($base, $type, $options);
                }

            });
        }

        $builder->addModelTransformer($this->baseAclTransformer->setEntityRecipient($options['entity_recipient']))
        ;

    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $labelClass = isset($view->vars['label_attr']['class']) ?? '';
        $labelClass .= 'text-bold';

        $view->vars['label_attr']['class'] = $labelClass;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'label' => 'app.acl.property.label',
        ]);
        $resolver->setRequired('entity_recipient');
    }
}