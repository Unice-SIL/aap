<?php


namespace App\Form\CallOfProject;


use App\Entity\CallOfProject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class DeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var CallOfProject $callOfProject */
        $callOfProject = $builder->getData();

        if (!$callOfProject instanceof CallOfProject) {
            throw new \Exception('You have to pass a ' . CallOfProject::class . ' instance as data.');
        }

        if ($callOfProject)
        $builder
            ->add('name', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'data-name' => $callOfProject->getName(),
                    'class' => 'name-field'
                ],
            ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $class = $view->vars['att']['class'] ?? '';
        $class .= ' delete-aap-form';

        $view->vars['attr']['class'] = $class;
    }
}