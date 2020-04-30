<?php

namespace App\DependencyInjection\Compiler;

use App\Widget\WidgetManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WidgetPass implements CompilerPassInterface
{

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has(WidgetManager::class)) {
            return;
        }

        $definition = $container->findDefinition(WidgetManager::class);

        $formWidgetTaggedServices = $container->findTaggedServiceIds('app.form_widget');

        foreach ($formWidgetTaggedServices as $id => $tags) {
            $definition->addMethodCall('addFormWidget', [new Reference($id)]);
        }

        $htmlWidgetTaggedServices = $container->findTaggedServiceIds('app.html_widget');

        foreach ($htmlWidgetTaggedServices as $id => $tags) {

            $definition->addMethodCall('addHtmlWidget', [new Reference($id)]);
        }
    }
}