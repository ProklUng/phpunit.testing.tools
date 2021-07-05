<?php

declare(strict_types=1);

namespace Prokl\TestingTools\Tools\ServiceMocker\Generator;

use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\ProxyGenerator\ProxyGeneratorInterface;

/**
 * Factory responsible of producing virtual proxy instances.
 *
 * @interal
 */
class GeneratorFactory extends LazyLoadingValueHolderFactory
{

    /**
     * @var LazyLoadingValueHolderGenerator  $generator
     */
    private $generator;

    /**
     * GeneratorFactory constructor.
     *
     * @param Configuration|null $configuration
     */
    public function __construct(?Configuration $configuration = null)
    {
        parent::__construct($configuration);

        $this->generator = new LazyLoadingValueHolderGenerator();
    }

    /**
     * Генератор.
     *
     * @return ProxyGeneratorInterface
     */
    protected function getGenerator(): ProxyGeneratorInterface
    {
        return $this->generator;
    }
}
