<?php

declare(strict_types=1);

namespace Prokl\TestingTools\Tools\ServiceMocker\Proxy;

/**
 * Class Proxy
 * @package Prokl\TestingTools\Tools\ServiceMocker\Proxy
 */
class Proxy
{
    /**
     * @var ProxyDefinition $definition Proxy definition.
     */
    private $definition;

    /**
     * Proxy constructor.
     *
     * @param ProxyDefinition $definition Proxy definition.
     */
    public function __construct(ProxyDefinition $definition)
    {
        $this->definition = $definition;
    }

    /**
     * @param mixed $method Метод.
     * @param mixed $args   Аргументы.
     *
     * @return mixed|ProxyDefinition
     */
    public function __call($method, $args)
    {
        $func = $this->definition->getMethodCallable($method);
        if (null === $func) {
            return $this->definition->getObject()->{$method}(...$args);
        } else {
            return call_user_func_array($func, $args);
        }
    }
}
