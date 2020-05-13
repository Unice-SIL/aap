<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'label' => 'app.user.property.username.label'
            ])
            ->add('email', null, [
                'label' => 'app.user.property.email.label'
            ])
            ->add('firstname', null, [
                'label' => 'app.user.property.firstname.label'
            ])
            ->add('lastname', null, [
                'label' => 'app.user.property.lastname.label'
            ])
            ->add('plainPassword', null, [
                'label' => 'app.user.property.plainPassword.label'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
