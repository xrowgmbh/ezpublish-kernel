<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler;

use eZ\Publish\Core\Compare\CompareEngineRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CompareEnginesRegistryPass implements CompilerPassInterface
{
    public const COMPARE_ENGINE_SERVICE_TAG = 'ezplatform.field_type.comparable.engine';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(CompareEngineRegistry::class)) {
            return;
        }

        $compareEngineRegistryDefinition = $container->getDefinition(CompareEngineRegistry::class);
        $comparableFieldTypeTags = $container->findTaggedServiceIds(self::COMPARE_ENGINE_SERVICE_TAG);

        foreach ($comparableFieldTypeTags as $id => $attributes) {
            foreach ($attributes as $attribute) {
                $compareEngineRegistryDefinition->addMethodCall(
                    'registerEngine',
                    [
                        $attribute['supported_type'],
                        new Reference($id),
                    ]
                );
            }
        }
    }
}
