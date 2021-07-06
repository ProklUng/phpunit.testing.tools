<?php

namespace Prokl\TestingTools\Tools\Container;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class TestKernel
 * @package Prokl\TestingTools\Tools\Container
 *
 * @since 06.07.2021
 */
class TestKernel extends Kernel
{
    /**
     * @inheritDoc
     */
    public function registerBundles()
    {

    }

    /**
     * @inheritDoc
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {

    }
}