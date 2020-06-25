<?php


namespace App\Form\Dictionary;


use App\Entity\Dictionary;
use App\Form\DictionaryContent\DictionaryContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DictionaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'app.dictionary.property.name.label'
            ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var Dictionary $dictionary */
            $dictionary = $event->getData();

            $entryOptions = [];

            if (null !== $dictionary and null === $dictionary->getId()) {
                $entryOptions['context'] = 'new';
            }

            $form->add('dictionaryContents', CollectionType::class, [
                'label' => 'app.dictionary.property.dictionary_contents.label',
                'entry_type' => DictionaryContentType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'entry_options' => $entryOptions
            ]);
        });
    }
}