<?php


namespace App\Twig;

use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

class WrapperRuntime implements RuntimeExtensionInterface
{
    /**
     * @var Environment
     */
    private $twigEngine;

    /**
     * WrapperRuntime constructor.
     * @param Environment $twigEngine
     */
    public function __construct(Environment $twigEngine)
    {
        $this->twigEngine = $twigEngine;
    }

    public function wrapperContainer($html, $container)
    {
        if (null === $container) {
            return $html;
        }
        return $this->twigEngine->display('partial/wrapper/_container.htm.twig', [
            'html' => $html,
            'container' => $container
        ]);
    }
}