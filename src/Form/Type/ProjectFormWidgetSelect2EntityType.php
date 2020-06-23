<?php


namespace App\Form\Type;


use App\Entity\ProjectFormWidget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class ProjectFormWidgetSelect2EntityType extends AbstractType
{
    public function getParent()
    {
        return Select2EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => false,
            'remote_route' => 'app.user.list_all_select_2',
            'class' => ProjectFormWidget::class,
            'primary_key' => 'id',
            'text_property' => 'title',
            'minimum_input_length' => 2,
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'cache' => true,
            'cache_timeout' => 60000, // if 'cache' is true
            'placeholder' => 'app.project_form_widget.select_2.placeholder',
        ]);
    }
}