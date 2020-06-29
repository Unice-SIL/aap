<?php


namespace App\Form\Widget\FormWidget;


use App\Entity\Dictionary;
use App\Form\DataTransformer\FormChoiceWidgetTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;


class FormChoiceWidgetType extends AbstractType
{
    private $formChoiceWidgetTransformer;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FormChoiceWidgetType constructor.
     * @param FormChoiceWidgetTransformer $formChoiceWidgetTransformer
     * @param TranslatorInterface $translator
     */
    public function __construct(FormChoiceWidgetTransformer $formChoiceWidgetTransformer, TranslatorInterface $translator)
    {
        $this->formChoiceWidgetTransformer = $formChoiceWidgetTransformer;
        $this->translator = $translator;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dictionary', EntityType::class, [
                'label' => 'app.dictionary_label',
                'class' => Dictionary::class,
                'required' => false
            ])
            ->add('file', FileType::class, [
                'mapped' => false,
                'label' => 'app.import_choices.label',
                'required' => false,
                'attr' => [
                    'data-list-selector' => '#choice-fields-list',
                    'data-wrong-format-message' => $this->translator->trans('app.import_choices.message.wrong_format'),
                    'data-no-data-message' => $this->translator->trans('app.import_choices.message.no_data'),
                    'data-error-message' => $this->translator->trans('app.import_choices.message.error_message'),
                ]
            ])
            ->add('choices', CollectionType::class, [
                'entry_type' => TextType::class,
                'label_format' => 'Option',
                'label' => 'Choices',
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'attr' => [
                        'class' => 'input-text-choice'
                    ]
                ]
            ])
            ->addModelTransformer($this->formChoiceWidgetTransformer)
            ->remove('placeholder')
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $widget = $event->getData();

            if (count($widget->getChoices()) === 0) {
                $widget->addChoice('');
            }

        });
    }

    public function getParent()
    {
        return FormWidgetType::class;
    }

}