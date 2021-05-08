<?php

namespace Prokl\TestingTools\Tools\FixtureGenerator;

use Illuminate\Container\Container;

/**
 * Class AbstractServiceProvider
 * @package Prokl\TestingTools\Tools\FixtureGenerator
 */
abstract class AbstractServiceProvider implements ServiceProviderContract
{
    /**
     * @var array $singletons
     */
    protected $singletons = [];

    /**
     * @var Container $container
     */
    protected $container;

    public function __construct()
    {
        $this->container = containerLaravel();

        $this->bindSingletons();
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
    }

    /**
     * bindSinletons.
     *
     * @access  protected
     * @return  void
     */
    protected function bindSingletons(): void
    {
        foreach ($this->singletons as $id => $implementation) {
            if (is_string($id)) {
                $this->container->singleton($id, $implementation);
            } else {
                $this->container->singleton($implementation);
            }
        }
    }
}
