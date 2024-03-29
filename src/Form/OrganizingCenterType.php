<?php

namespace App\Form;

use App\Entity\OrganizingCenter;
use App\Entity\User;
use App\Form\Type\BaseAclType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizingCenterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, [
                    'label' => 'app.organizing_center.property.name.label'
                ]
            )
            ->add('acls', BaseAclType::class, [
                'entity_recipient' => $builder->getData()
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
