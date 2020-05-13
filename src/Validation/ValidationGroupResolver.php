<?php


namespace App\Validation;

use Symfony\Component\Form\FormInterface;

class ValidationGroupResolver
{

    /**
     * @param FormInterface $form
     * @return array
     */
    public function __invoke(FormInterface $form)
    {
        $groups = [];

        $entity = $form->getData();

        if (null !== $entity and null === $entity->getId()) {

            $groups[] = 'new';
        }

        return $groups;
    }
}