<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class AppExtension
 * @package App\Twig
 */
class AppExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('wrapper_container', [WrapperRuntime::class, 'wrapperContainer']),
            new TwigFilter('humanize_data', [HumanizeRuntime::class, 'humanizeData']),
            new TwigFilter('array_unique', [$this, 'arrayUnique']),
        ];
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('instanceof', [$this, 'instanceof']),
            new TwigFunction('render_breadcrumb', [BreadcrumbRuntime::class, 'renderBreadcrumb']),
            new TwigFunction('batch_action_render_input', [BatchActionRuntime::class, 'batchActionRenderInput']),
        ];
    }

    /**
     * @param $instance
     * @param $classFulName
     * @return bool
     */
    public function instanceof($instance, $classFulName) {

        return $instance instanceof $classFulName;
    }

    /**
     * @param array $array
     * @return array
     */
    public function arrayUnique(array $array)
    {
        return array_unique($array);
    }
}
