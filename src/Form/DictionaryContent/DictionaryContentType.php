<?php


namespace App\Form\DictionaryContent;


use App\Entity\DictionaryContent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DictionaryContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if ($options['context'] === 'new') {

            $builder->add('code', null, [
                'attr' => [
                    'placeholder' => 'app.dictionary_content.property.code.label'
                ]
            ]);
        }

        $builder->add('value', null, [
                'attr' => [
                    'placeholder' => 'app.dictionary_content.property.value.label'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DictionaryContent::class,
            'context' => 'edit'
        ]);
    }
}