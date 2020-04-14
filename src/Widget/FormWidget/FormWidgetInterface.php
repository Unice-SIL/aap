<?php

namespace App\Widget\FormWidget;

use App\Widget\WidgetInterface;
use Symfony\Component\Form\DataTransformerInterface;

interface FormWidgetInterface extends WidgetInterface
{
    public function getSymfonyType(): string;

    public function getDataTransformer(): DataTransformerInterface;
}
