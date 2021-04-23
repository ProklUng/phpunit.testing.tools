<?php

namespace Prokl\TestingTools\Tools\Container;

use Exception;
use LogicException;
use ReflectionObject;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

/**
 * Class BuildContainer
 * @package Prokl\TestingTools\Tools\Container
 *
 * @since 23.04.2021
 */
final class BuildContainer
{
    /**
     * @var ContainerBuilder $container Контейнер.
     */
    private $container;

    /**
     * @var array $configs Конфиги.
     */
    private $configs;

    /**
     * @var string $basePathConfig Базовый путь к конфигам.
     */
    private $basePathConfig = __DIR__ . '/../../../../Resources/config';

    /**
     * @var string $projectDir
     */
    private $projectDir;

    /**
     * BuildContainer constructor.
     *
     * @param array $yamlConfigs Конфиги.
     */
    public function __construct(array $yamlConfigs)
    {
        $this->configs = $yamlConfigs;

        $this->container = new ContainerBuilder();

        $defaultParams = [
            'kernel.project_dir' => realpath($this->getProjectDir()) ?: $this->getProjectDir(),
            'kernel.debug' => true,
            'kernel.cache_dir' => $this->getCacheDir(),
            'kernel.site.host' => $_SERVER['HTTP_HOST'],
            'kernel.http.host' => $this->getSiteHost(),
            'kernel.schema' => 'http://',
        ];

        foreach ($defaultParams as $key => $defaultParam) {
            $this->container->setParameter($key, $defaultParam);
        }
    }

    /**
     * Статический конструктор.
     *
     * @param array       $yamlConfigs    Конфиги.
     * @param string|null $basePathConfig Базовый путь к конфигам.
     *
     * @return ContainerBuilder
     * @throws Exception
     */
    public static function getTestContainer(array $yamlConfigs, ?string $basePathConfig = null) : ContainerBuilder
    {
        $self = new self($yamlConfigs);

        if ($basePathConfig) {
            $self->setBasePathConfig($basePathConfig);
        }

        return $self->build();
    }

    /**
     * @return ContainerBuilder
     * @throws Exception
     */
    public function build() : ContainerBuilder
    {
        $compilerPass = new PassConfig();

        foreach ($compilerPass->getPasses() as $pass) {
            $this->container->addCompilerPass($pass);
        }

        foreach ($this->configs as $config) {
            $this->loadContainerConfig($config);
        }

        $this->autoconfigure();

        $this->container->compile(true);

        return $this->container;
    }

    /**
     * Gets the application root dir (path of the project's composer file).
     *
     * @return string The project root dir
     */
    public function getProjectDir(): string
    {
        if (!$this->projectDir) {
            $r = new ReflectionObject($this);

            if (!file_exists($dir = (string)$r->getFileName())) {
                throw new LogicException(
                    sprintf('Cannot auto-detect project dir for kernel of class "%s".', $r->name)
                );
            }

            $dir = $rootDir = dirname($dir);
            while (!file_exists($dir . '/composer.json')) {
                if ($dir === dirname($dir)) {
                    return $this->projectDir = $rootDir;
                }
                $dir = dirname($dir);
            }

            $this->projectDir = $dir;
        }

        return $this->projectDir;
    }

    /**
     * Директория кэша.
     *
     * @return string
     * @throws RuntimeException
     *
     * @since 13.12.2020 Доработка.
     */
    public function getCacheDir(): string
    {
        $cachePath = __DIR__ . $this->getRelativeCacheDir();
        if (!file_exists($cachePath) && !mkdir($cachePath) && !is_dir($cachePath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $cachePath));
        }

        return $cachePath;
    }

    /**
     * Относительная директория кэша.
     *
     * @return string
     *
     * @since 13.12.2020
     */
    public function getRelativeCacheDir(): string
    {
        return '/cache/';
    }

    /**
     * Путь к конфигам. Относительно $projectDir.
     *
     * @param string $basePathConfig
     */
    public function setBasePathConfig(string $basePathConfig): void
    {
        $this->basePathConfig = $this->getProjectDir() . $basePathConfig;
    }

    /**
     * Хост сайта.
     *
     * @return string
     *
     * @since 08.10.2020
     */
    private function getSiteHost() : string
    {
        return 'http://' . (array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : '');
    }

    /**
     * Загрузка конфигурационного файла контейнера.
     *
     * @param string $fileName Конфигурационный файл.
     *
     * @return boolean
     * @throws Exception
     *
     */
    private function loadContainerConfig(string $fileName) : bool
    {
        $loader = new YamlFileLoader(
            $this->container,
            new FileLocator($this->basePathConfig)
        );

        $loader->load($fileName);

        return true;
    }

    /**
     * Autoconfigure tags.
     *
     * @return void
     */
    private function autoconfigure(): void
    {
        $autoConfigure = [
            'controller.service_arguments' => AbstractController::class,
            'controller.argument_value_resolver' => ArgumentValueResolverInterface::class,
            'container.service_locator' => ServiceLocator::class,
            'kernel.event_subscriber' => EventSubscriberInterface::class,
        ];

        foreach ($autoConfigure as $tag => $class) {
            $this->container->registerForAutoconfiguration($class)
                ->addTag($tag);
        }
    }

    /**
     * Рекурсивно удалить директорию.
     *
     * @param string $dir
     *
     * @return void
     */
    public static function rrmdir(string $dir) : void
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object !== "." && $object !== "..") {
                    if (is_dir($dir.DIRECTORY_SEPARATOR.$object) && !is_link($dir."/".$object)) {
                        self::rrmdir($dir.DIRECTORY_SEPARATOR.$object);
                    } else {
                        unlink($dir.DIRECTORY_SEPARATOR.$object);
                    }
                }
            }
            rmdir($dir);
        }
    }
}
