<?php


namespace App\Form\Type;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class UserSelect2EntityType extends AbstractType
{
    public function getParent()
    {
        return Select2EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => true,
            'remote_route' => 'app.admin.user.list_all_select_2',
            'class' => User::class,
            'primary_key' => 'id',
            'text_property' => 'username',
            'minimum_input_length' => 2,
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'cache' => true,
            'cache_timeout' => 60000, // if 'cache' is true
            'placeholder' => 'app.user.select_2.placeholder',
        ]);
    }
}