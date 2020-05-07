<?php


namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteFileCheckboxType extends AbstractType
{

    public function getParent()
    {
        return CheckboxType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['client_original_name'] = $options['client_original_name'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'app.action.delete',
            'required' => false,
            'client_original_name' => null,
        ]);
    }
}