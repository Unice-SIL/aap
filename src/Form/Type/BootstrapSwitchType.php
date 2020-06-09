<?php


namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class BootstrapSwitchType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * BootstrapSwitchType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getParent()
    {
        return CheckboxType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $class = $view->vars['attr']['class'] ?? '';
        $class .= ' bootstrap-switch';

        $view->vars['attr']['class'] = $class;
        $view->vars['attr']['data-on-text'] = $view->vars['attr']['data-on-text'] ?? $this->translator->trans('app.form.type.bootstrap_switch.yes');
        $view->vars['attr']['data-off-text'] = $view->vars['attr']['data-off-text'] ?? $this->translator->trans('app.form.type.bootstrap_switch.no');

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'false_values' => ['false', null],
        ]);
    }
}