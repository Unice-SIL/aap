<?php


namespace App\Utils\Batch;


use Symfony\Component\Form\FormInterface;

interface BatchActionInterface
{
    public function getName(): string;

    public function process($entity, FormInterface $form);

    public function supports($entity): bool;

    public function getFormClassName(): string;

}