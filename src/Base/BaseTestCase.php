<?php

namespace Prokl\TestingTools\Base;

use Exception;
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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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
     * @var array $matched_dirs
     */
    private $matched_dirs = [];

    /**
     * @inheritDoc
     * @throws Exception
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

                // Глобалы могут быть установлены в конфиге phpunit.
                if ($_SERVER['HTTP_HOST'] === null) {
                    $_SERVER['HTTP_HOST'] = $currentHttpHost;
                }

                if ($_SERVER['SERVER_NAME'] === null) {
                    $_SERVER['SERVER_NAME'] = $currentHttpHost;
                }

                $this->container->get('request')->setServer(
                    'HTTP_HOST',
                    $currentHttpHost
                );

                $this->container->get('request')->setServer(
                    'SERVER_NAME',
                    $currentHttpHost
                );
            }

            $_GET  = $_POST = [];

            if (!array_key_exists('REMOTE_ADDR', $_SERVER)) {
                $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            }

            if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
                $_SERVER['REQUEST_METHOD'] = 'GET';
            }

            if (!array_key_exists('REQUEST_URI', $_SERVER)) {
                $_SERVER['REQUEST_URI'] = '';
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
     * Creates a unique temporary file name.
     *
     * The directory in which the file is created depends on the environment configuration.
     *
     * @return string|boolean Path on success, else false.
     */
    public function tempFilename()
    {
        $tmp_dir = '';
        $dirs = ['TMP', 'TMPDIR', 'TEMP'];

        foreach ($dirs as $dir) {
            if (isset($_ENV[$dir]) && !empty($_ENV[$dir])) {
                $tmp_dir = $dir;
                break;
            }
        }

        if (empty($tmp_dir)) {
            $tmp_dir = sys_get_temp_dir();
        }

        $tmp_dir = realpath($tmp_dir);

        return tempnam($tmp_dir, 'wpunit');
    }

    /**
     * Returns a list of all files contained inside a directory.
     *
     * @param string $dir Path to the directory to scan.
     *
     * @return array List of file paths.
     */
    protected function filesInDir(string $dir): array
    {
        $files = [];

        $iterator = new RecursiveDirectoryIterator($dir);
        $objects = new RecursiveIteratorIterator($iterator);
        foreach ($objects as $name => $object) {
            if (is_file($name)) {
                $files[] = $name;
            }
        }

        return $files;
    }

    /**
     * Deletes all directories contained inside a directory.
     *
     * @param string $path Path to the directory to scan.
     *
     * @return void
     *
     */
    protected function deleteFolders(string $path) : void
    {
        $this->matched_dirs = [];
        if (!is_dir($path)) {
            return;
        }

        $this->scandir($path);
        foreach (array_reverse($this->matched_dirs) as $dir) {
            rmdir($dir);
        }

        rmdir($path);
    }

    /**
     * Retrieves all directories contained inside a directory and stores them in the `$matched_dirs` property. Hidden
     * directories are ignored.
     *
     * This is a helper for the `delete_folders()` method.
     *
     * @param string $dir Path to the directory to scan.
     *
     * @return void
     */
    protected function scandir(string $dir) : void
    {
        foreach (scandir($dir) as $path) {
            if (0 !== strpos($path, '.') && is_dir($dir . '/' . $path)) {
                $this->matched_dirs[] = $dir . '/' . $path;
                $this->scandir($dir . '/' . $path);
            }
        }
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
