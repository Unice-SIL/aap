<?php

namespace App\Widget\FormWidget;

use App\Widget\WidgetInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;

interface FormWidgetInterface extends WidgetInterface
{
    public function getType(): string;

    public function getForm(): FormInterface;

    public function getTemplate(): string;

    public function getSymfonyType(): string;

    public function getDataTransformer(): DataTransformerInterface;
}
