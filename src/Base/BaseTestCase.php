<?php

namespace Prokl\TestingTools\Base;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseTestCase
 * @package Prokl\TestingTools\Base
 */
class BaseTestCase extends TestCase
{
    /**
     * @var mixed $obTestObject
     */
    protected $obTestObject;

    /**
     * @var Generator | null $faker
     */
    protected $faker;

    /**
     * @var ContainerInterface $testContainer Тестовый контейнер.
     */
    protected static $testContainer;

    /**
     * @var Container $container Контейнер DI Symfony.
     */
    protected $container;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        // Контейнер DI Symfony.
        if (function_exists('container')) {
            $this->container = container();
            if ($this->container->has('test.service_container')) {
                // Инициализация тестового контейнера.
                static::$testContainer = $this->container->get('test.service_container')
                    ?: $this->container;
            }

            $currentHttpHost = $this->container->getParameter('local.http.host');

            $_SERVER['HTTP_HOST'] = $currentHttpHost;
            $_SERVER['SERVER_NAME'] = $currentHttpHost;

            $this->container->get('request')->setServer(
                'HTTP_HOST',
                $currentHttpHost
            );

            $this->container->get('request')->setServer(
                'SERVER_NAME',
                $currentHttpHost
            );

            $this->container->get('request')->setServer(
                'HTTP_HOST',
                $currentHttpHost
            );
        }

        Mockery::resetContainer();
        parent::setUp();

        $this->faker = Factory::create();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        if (static::$testContainer !== null) {
            static::$testContainer->reset();
        }

        parent::tearDown();

        Mockery::close();
    }

}
