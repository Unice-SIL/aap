<?php


namespace App\Form\MailTemplate;


use App\Form\Type\SummernoteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MailTemplateType extends AbstractType
{
     public function buildForm(FormBuilderInterface $builder, array $options)
     {
         $builder
             ->add('subject', TextType::class, [
                 'label' => 'app.mail_template.property.subject.label'
             ])
             ->add('body', SummernoteType::class, [
                 'label' => 'app.mail_template.property.body.label'
             ])
         ;
     }
}