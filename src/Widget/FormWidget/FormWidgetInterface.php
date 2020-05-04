<?php

namespace App\Widget\FormWidget;

use App\Widget\WidgetInterface;

interface FormWidgetInterface extends WidgetInterface
{
    public function getSymfonyType(): string;

    public function getLabel(): ?string;

    public function getVisibilityLabel(): bool;

    public function getConstraints(): array;

    public function getDynamicConstraintsType(): ?string;

    public function getOptions(): array;

    public function transformData($value);

    public function reverseTransformData($value);

    public function renderView($value): ?string;
}
