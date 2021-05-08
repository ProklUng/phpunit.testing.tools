<?php

namespace Prokl\TestingTools\Tools\FixtureGenerator;

/**
 * Class FixtureServiceProvider
 * @package Prokl\TestingTools\Tools\FixtureGenerator
 */
class FixtureServiceProvider extends AbstractServiceProvider
{
    /** Путь к фикстурам */
    private const PATH_FIXTURES = '/local/classes/Tests/Fixtures';

    /**
     * @var string $path
     */
    protected $path;

    /**
     * @var array $singletons
     */
    protected $singletons = [];

    public function __construct()
    {
        $this->singletons = [
            FixtureManager::class => function () {
                return new FixtureManager($this->path);
            }
        ];

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        parent::register();

        $this->loadPath();
    }

    /**
     * Loads fixture path
     *
     * @access  protected
     * @return  void
     */
    protected function loadPath(): void
    {
        $this->path = $_SERVER['DOCUMENT_ROOT']. self::PATH_FIXTURES;
    }
}
