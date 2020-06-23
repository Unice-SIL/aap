<?php


namespace App\Form\Type;


use App\Entity\CallOfProject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class CallOfProjectSelect2EntityType extends AbstractType
{
    public function getParent()
    {
        return Select2EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => false,
            'remote_route' => 'app.call_of_project.list_by_user_select_2',
            'class' => CallOfProject::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'minimum_input_length' => 2,
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'cache' => true,
            'cache_timeout' => 60000, // if 'cache' is true
            'placeholder' => 'app.call_of_project.select_2.placeholder',
            'mapped' => false,
            'label' => 'app.call_of_project.init.choices.init_by_call_of_project',
            'required' => true,
        ]);
    }
}