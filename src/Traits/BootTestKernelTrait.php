<?php

namespace Prokl\TestingTools\Traits;

use Prokl\TestingTools\Tools\Container\TestKernel;
use ReflectionObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Trait BootTestKernelTrait
 * @package Prokl\TestingTools\Traits
 *
 * @since 06.07.2021
 */
trait BootTestKernelTrait
{
    /**
     * Инициализировать тестовый Kernel.
     *
     * @param ContainerBuilder $container Контейнер.
     * @param string           $env       Окружение.
     * @param boolean          $debug     Отладка.
     *
     * @return Kernel
     */
    protected function bootTestKernel(
        ContainerBuilder $container,
        string $env = 'dev',
        bool $debug = true
    ): Kernel {
        $kernel = new TestKernel($env, $debug);

        // Установить контейнер в Kernel.
        $reflection = new ReflectionObject($kernel);
        $property = $reflection->getProperty('container');
        $property->setAccessible(true);
        $property->setValue($kernel, $container);

        // Установить booted в true.
        $property = $reflection->getProperty('booted');
        $property->setAccessible(true);
        $property->setValue($kernel, true);

        // Установить сервис kernel.
        $reflection = new ReflectionObject($container);
        $property = $reflection->getProperty('services');
        $property->setAccessible(true);
        $services = $property->getValue($container);
        $services['kernel'] = $kernel;
        $property->setValue($container, $services);

        return $kernel;
    }
}