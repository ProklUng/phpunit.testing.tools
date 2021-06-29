<?php

namespace Prokl\TestingTools\Traits;

use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Trait ServiceLocatorConstructorTrait
 * @package Prokl\TestingTools\Traits
 *
 * @since 29.06.2021
 */
trait ServiceLocatorConstructorTrait
{
    /**
     * Получить сервис-локатор на базе анонимного класса.
     *
     * @param array $config Конфигурация вида:
     *
     * 'key' => ClassName::class
     *  или
     * 'key' => $object
     *
     * @return ServiceLocator
     */
    protected function constructServiceLocator(array $config) : ServiceLocator
    {
        $result = [];
        foreach ($config as $key => $class) {
            $result[$key] = function () use ($class) {
                return is_object($class) ? $class : new $class;
            };
        }

        return new class($result) extends ServiceLocator {
        };
    }
}
