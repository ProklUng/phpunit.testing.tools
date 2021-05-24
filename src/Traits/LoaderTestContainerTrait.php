<?php

namespace Prokl\TestingTools\Traits;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Trait LoaderTestContainerTrait
 * @package Prokl\Testing\Tools\
 *
 * Еще один способ загрузить тестовый контейнер.
 * @since 20.01.2021
 */
trait LoaderTestContainerTrait
{
    /**
     * @var array $containers Контейнеры.
     */
    private static $containers = [];

    /**
     * @var ContainerBuilder $buildedContainer Конечный контейнер.
     */
    private $buildedContainer;

    /**
     * @var string $testBasePath
     */
    private $testBasePath = '';

    /**
     * @var string $testsDirectory
     */
    private $testsDirectory = '';

    /**
     * Задать тестовый контейнер.
     *
     * @param ContainerBuilder $container Контейнер.
     *
     * @return void
     */
    protected function setContainer(ContainerBuilder $container): void
    {
        $this->buildedContainer = $container;
    }

    /**
     * Изначальная конфигурация тестового контейнера.
     *
     * @return void
     */
    protected function configureContainer(): void
    {
        $this->buildedContainer->setParameter('base.path', $this->getBasePath());
    }

    /**
     * @param string $path Путь.
     *
     * @return void
     */
    protected function setTestBasePath(string $path): void
    {
        $this->testBasePath = $path;
    }

    /**
     * Директория, где лежат тесты.
     *
     * @return string
     */
    protected function getTestDirectory(): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/tests';
    }

    /**
     * @return string
     */
    protected function getTestBasePath(): string
    {
        if ($this->testBasePath === '') {
            $testDirectory = $this->getTestDirectory();

            if (strtolower(basename($testDirectory)) === 'tests') {
                $basePath = $testDirectory . '/';
            } else {
                $basePath = substr(
                    $testDirectory,
                    0,
                    strripos($testDirectory, $this->testsDirectory) + strlen($this->testsDirectory)
                );
            }

            $this->setTestBasePath($basePath);
        }

        return $this->testBasePath;
    }

    /**
     * @return string
     */
    protected function getBasePath() : string
    {
        return realpath($this->getTestBasePath() . '/..');
    }

    /**
     * Сервис из тестового контейнера.
     *
     * @param string $service Сервис.
     *
     * @return object|null
     * @throws Exception
     */
    protected function get(string $service)
    {
        return $this->getContainer()->get($service);
    }

    /**
     * @return ContainerBuilder
     * @throws Exception
     */
    protected function getContainer(): ContainerBuilder
    {
        if (isset(self::$containers[$this->getTestBasePath()])) {
            // Use cached container (creating a new container from yml is too slow)
            $this->setContainer(self::$containers[$this->getTestBasePath()]);

            $this->buildedContainer->reset();

            $this->configureContainer();
        } elseif (!$this->buildedContainer) {
            // Create new container
            $this->setContainer(new ContainerBuilder());

            $this->configureContainer();

            $locator = new FileLocator($this->getTestBasePath());
            $loader = new YamlFileLoader($this->buildedContainer, $locator);

            $loader->load('config.yml');

            try {
                $loader->load('config.local.yml');
            } catch (\InvalidArgumentException $e) {
                // No local config found
            }

            // Cache container
            self::$containers[$this->getTestBasePath()] = $this->buildedContainer;
        }

        return $this->buildedContainer;
    }
}
