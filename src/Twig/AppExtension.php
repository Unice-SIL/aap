<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('wrapper_container', [WrapperRuntime::class, 'wrapperContainer']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('instanceof', [$this, 'instanceof']),
        ];
    }

    public function instanceof($instance, $classFulName) {

        return $instance instanceof $classFulName;
    }
}
