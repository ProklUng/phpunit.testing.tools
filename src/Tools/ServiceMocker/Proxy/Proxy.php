<?php

declare(strict_types=1);

namespace Prokl\TestingTools\Tools\ServiceMocker\Proxy;

/**
 * @internal
 */
class Proxy
{
    /**
     * @var ProxyDefinition $definition
     */
    private $definition;

    /**
     * Proxy constructor.
     *
     * @param ProxyDefinition $definition
     */
    public function __construct(ProxyDefinition $definition)
    {
        $this->definition = $definition;
    }

    /**
     * @param mixed $method
     * @param mixed $args
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
