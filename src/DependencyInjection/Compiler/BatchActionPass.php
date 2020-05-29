<?php

namespace App\DependencyInjection\Compiler;

use App\Utils\Batch\BatchActionManagerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BatchActionPass implements CompilerPassInterface
{

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has(BatchActionManagerInterface::class)) {
            return;
        }

        $definition = $container->findDefinition(BatchActionManagerInterface::class);

        $batchActionServices = $container->findTaggedServiceIds('app.batch_action');

        foreach ($batchActionServices as $id => $tags) {
            $definition->addMethodCall('addBatchAction', [new Reference($id)]);
        }

    }
}