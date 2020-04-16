<?php

namespace App\Widget;

interface WidgetInterface
{
    const TYPE_TEXT = 'text';
    const TYPE_NUMBER = 'number';
    const TYPE_DATE = 'date';

    public function getName(): string;

    public function getFormType(): string;

    public function getTemplate(): string;

    public function getType(): string;
}