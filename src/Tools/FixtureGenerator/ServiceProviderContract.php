<?php

namespace Prokl\TestingTools\Tools\FixtureGenerator;

/**
 * Interface ServiceProviderContract
 * @package Prokl\TestingTools\Tools\FixtureGenerator
 */
interface ServiceProviderContract
{
    /**
     * Registers the service dependencies in the application
     *
     * @access  public
     * @return  void
     */
    public function register(): void;
}
