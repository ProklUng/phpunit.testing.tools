<?php

namespace Prokl\TestingTools\Base;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use PHPUnit\Framework\TestCase;
use Prokl\TestingTools\Tools\Macros\MacrosInit;
use Prokl\TestingTools\Tools\PHPMockerFunctions;
use Prokl\TestingTools\Traits\DataProvidersTrait;
use Prokl\TestingTools\Traits\ExceptionAsserts;
use Prokl\TestingTools\Traits\LoaderTestContainerTrait;
use Prokl\TestingTools\Traits\PHPUnitTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseTestCase
 * @package Prokl\TestingTools\Base
 */
class BaseTestCase extends TestCase
{
    use PHPUnitTrait;
    use ExceptionAsserts;
    use LoaderTestContainerTrait;
    use DataProvidersTrait;

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
     * @var PHPMockerFunctions $mockerFunctions Мокер функций.
     */
    protected $mockerFunctions;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        // Контейнер DI Symfony.
        if ($this->container !== null) {
            if ($this->container->has('test.service_container')) {
                // Инициализация тестового контейнера.
                static::$testContainer = $this->container->get('test.service_container')
                    ?: $this->container;
            }

            if ($this->container->has('local.http.host')) {
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
        }

        Mockery::resetContainer();
        parent::setUp();

        $this->faker = Factory::create();

        $this->mockerFunctions = new PHPMockerFunctions();
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
        // Сбросить замоканные функции.
        $this->mockerFunctions->shutdown();
    }

    /**
     * Инициализировать макросы.
     *
     * @beforeClass
     */
    public static function setUpMacroses(): void
    {
        $macroses = new MacrosInit();
        $macroses->init();
    }

}
