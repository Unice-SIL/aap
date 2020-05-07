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

    public function transformData($value, array $options = []);

    public function reverseTransformData($value, array $options = []);

    public function renderView($value): ?string;

    public function isFileWidget(): bool;
}
