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
            new TwigFilter('humanize_data', [HumanizeRuntime::class, 'humanizeData']),
            new TwigFilter('array_unique', [$this, 'arrayUnique']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('instanceof', [$this, 'instanceof']),
            new TwigFunction('render_breadcrumb', [BreadcrumbRuntime::class, 'renderBreadcrumb']),
        ];
    }

    public function instanceof($instance, $classFulName) {

        return $instance instanceof $classFulName;
    }

    public function arrayUnique(array $array)
    {
        return array_unique($array);
    }
}
