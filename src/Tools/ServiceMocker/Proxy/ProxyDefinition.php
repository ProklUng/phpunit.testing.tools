<?php

declare(strict_types=1);

namespace Prokl\TestingTools\Tools\ServiceMocker\Proxy;

use LogicException;

/**
 * @internal
 */
class ProxyDefinition
{
    private $originalObject;
    private $methods = [];
    private $methodsQueue = [];
    private $replacement;

    /**
     * ProxyDefinition constructor.
     *
     * @param object $originalObject
     */
    public function __construct(object $originalObject)
    {
        $this->originalObject = $originalObject;
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->methods = [];
        $this->methodsQueue = [];
        $this->replacement = null;
    }

    /**
     * @param object $replacement Замена.
     *
     * @return void
     */
    public function swap(object $replacement): void
    {
        $this->clear();
        $this->replacement = $replacement;
    }

    /**
     * Get an object to execute a method on.
     *
     * @return void
     */
    public function getObject(): object
    {
        return $this->replacement ?? $this->originalObject;
    }

    /**
     * @return object
     */
    public function getOriginalObject(): object
    {
        return $this->originalObject;
    }

    /**
     * @param mixed $originalObject
     *
     * @internal
     */
    public function setOriginalObject($originalObject): void
    {
        $this->originalObject = $originalObject;
    }

    /**
     * @param string $method Метод.
     *
     * @return callable|null
     */
    public function getMethodCallable(string $method): ?callable
    {
        if (isset($this->methodsQueue[$method])) {
            $key = array_key_first($this->methodsQueue[$method]);
            if (null !== $key) {
                $func = $this->methodsQueue[$method][$key];
                unset($this->methodsQueue[$method][$key]);

                return $func;
            }
        }

        if (isset($this->methods[$method])) {
            return $this->methods[$method];
        }

        return null;
    }

    /**
     * @param string   $method Метод.
     * @param callable $func   Callback.
     *
     * @return void
     */
    public function addMethod(string $method, callable $func): void
    {
        if ($this->replacement) {
            throw new LogicException('Cannot add a method after added a replacement');
        }

        $this->methods[$method] = $func;
    }

    /**
     * @param string $method Метод.
     *
     * @return void
     */
    public function removeMethod(string $method): void
    {
        unset($this->methods[$method]);
    }

    /**
     * @param string   $method Метод.
     * @param callable $func   Callback.
     *
     * @return void
     */
    public function appendMethodsQueue(string $method, callable $func): void
    {
        if ($this->replacement) {
            throw new LogicException('Cannot add a method after added a replacement');
        }

        $this->methodsQueue[$method][] = $func;
    }

    /**
     * @param string $method Метод.
     *
     * @return void
     */
    public function clearMethodsQueue(string $method): void
    {
        unset($this->methodsQueue[$method]);
    }
}
