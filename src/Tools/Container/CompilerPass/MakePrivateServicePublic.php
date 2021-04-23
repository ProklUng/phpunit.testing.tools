<?php

namespace Prokl\TestingTools\Tools\Container\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class MakePrivateEventsPublic
 * @package Prokl\TestingTools\Tools\Container\CompilerPass
 *
 */
class MakePrivateServicePublic implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container) : void
    {
        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }
    }
}
