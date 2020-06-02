<?php


namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Select2Type extends AbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;


    /**
     * Select2Type constructor.
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $class = $view->vars['attr']['class'] ?? '';
        $class .= 'select-2';

        $view->vars['attr']['class'] = $class;
        $view->vars['attr']['data-url'] = $this->urlGenerator->generate($options['remote_route']);
        $view->vars['attr']['data-minimum-input-length'] = $options['minimum_input_length'];
        $view->vars['attr']['data-language'] = $options['language'];
        $view->vars['attr']['data-multiple'] = $options['multiple'];
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
         $resolver->setDefaults([
             'remote_route' => 'app.acl.list_user_and_group_select_2',
             'minimum_input_length' => 2,
             'language' => 'fr',
             'multiple' => false,
             'required' => false
         ]);
    }
}