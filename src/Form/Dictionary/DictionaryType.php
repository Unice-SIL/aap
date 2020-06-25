<?php


namespace App\Form\Dictionary;


use App\Form\DictionaryContent\DictionaryContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class DictionaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'app.dictionary.property.name.label'
            ])
            ->add('dictionaryContents', CollectionType::class, [
                'label' => 'app.dictionary.property.dictionary_contents.label',
                'entry_type' => DictionaryContentType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }
}