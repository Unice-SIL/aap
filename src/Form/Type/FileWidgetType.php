<?php


namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FileWidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class, $options['options']);

        // add delete only if there is a file
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
            $form = $event->getForm();

            $object = $event->getData();

            // no object or no uploaded file: no delete button
            if (null === $object or null === $object['file']) {
                return;
            }

            if (isset($options['options']['constraints'])) {
                foreach ($options['options']['constraints'] as $key => $constraint) {
                    if ($constraint instanceof NotBlank) {
                        unset($options['options']['constraints'][$key]);
                    }
                }
            }

            $form->add('file', FileType::class, $options['options']);

            $form->add('delete', DeleteFileCheckboxType::class, [
                'client_original_name' => $object['file']->getClientOriginalName()
            ]);
        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'options' => [],
        ]);
    }
}