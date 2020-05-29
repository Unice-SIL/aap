<?php


namespace App\Form\Group;


use App\Form\Type\UserSelect2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'app.group.property.name.label',
            ])
            ->add('members', UserSelect2EntityType::class)
        ;
    }
}