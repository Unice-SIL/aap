<?php

namespace App\Form;

use App\Entity\OrganizingCenter;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class OrganizingCenterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                    'label' => 'app.organizing_center.property.name.label'
                ]
            )
            ->add('members', Select2EntityType::class, [
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
                'label' => 'app.organizing_center.property.members.label',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrganizingCenter::class,
        ]);
    }
}
