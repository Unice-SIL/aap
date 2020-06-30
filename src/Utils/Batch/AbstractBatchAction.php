<?php


namespace App\Utils\Batch;


use Symfony\Component\Form\FormInterface;

abstract class AbstractBatchAction implements BatchActionInterface
{
    public function process($entity, FormInterface $form)
    {
        if (!$this->supports($entity)) {
            return;
        }

        $this->processOnEntity($entity, $form);
    }

    abstract public function getName(): string;

    abstract public function supports($entity): bool;

    abstract protected function processOnEntity($entity, FormInterface $form);

    abstract function getFormClassName(): string;

}