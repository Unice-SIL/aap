<?php

namespace App\Widget;

interface WidgetInterface
{
    public function getName(): string;

    public function getFormType(): string;

    public function getTemplate(): string;
}