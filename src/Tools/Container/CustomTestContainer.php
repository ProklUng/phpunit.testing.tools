<?php

namespace Prokl\TestingTools\Tools\Container;

use ReflectionException;
use ReflectionObject;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CustomTestContainer
 * @package Prokl\TestingTools\Tools\Container
 *
 * @since 18.11.2020
 */
class CustomTestContainer extends TestContainer
{
    /**
     * @var ContainerInterface $testContainer Контейнер.
     */
    private $testContainer;

    /**
     * @var ContainerInterface $backupOriginalContainer Бэкап контейнера.
     */
    private $backupOriginalContainer;

    /**
     * @inheritDoc
     * @throws ReflectionException Ошибки рефлексии.
     */
    public function set($id, $service) : void
    {
        $reflection = new ReflectionObject($this->testContainer);
        $property = $reflection->getProperty('services');
        $property->setAccessible(true);

        $services = $property->getValue($this->testContainer);

        $services[$id] = $service;

        $property->setValue($this->testContainer, $services);
    }

    /**
     * Задать тестовый контейнер.
     *
     * @param ContainerInterface $container Контейнер.
     *
     * @return void
     */
    public function setTestContainer(ContainerInterface $container) : void
    {
        $this->testContainer = $this->backupOriginalContainer = $container;
    }

    /**
     * @inheritDoc
     * @throws ReflectionException Ошибки рефлексии.
     */
    public function reset() : void
    {
        $reflection = new ReflectionObject($this->testContainer);
        $property = $reflection->getProperty('services');
        $property->setAccessible(true);

        $property->setValue($this->testContainer, null);

        $this->testContainer = $this->backupOriginalContainer;
    }
}
