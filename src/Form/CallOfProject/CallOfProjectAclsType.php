<?php


namespace App\Form\CallOfProject;


use App\Form\Type\BaseAclType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CallOfProjectAclsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('acls', BaseAclType::class, [
            'entity_recipient' => $builder->getData()
        ]);
    }
}