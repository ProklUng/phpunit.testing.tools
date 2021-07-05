<?php

declare(strict_types=1);

namespace Prokl\TestingTools\Tools\ServiceMocker;

use Prokl\TestingTools\Tools\ServiceMocker\Proxy\Proxy;
use Prokl\TestingTools\Tools\ServiceMocker\Proxy\ProxyDefinition;
use ProxyManager\Proxy\LazyLoadingInterface;

/**
 * Class ServiceMock
 * @package Prokl\TestingTools\Tools\ServiceMocker
 */
class ServiceMock
{
    /**
     * @var array $definitions
     */
    private static $definitions = [];

    /**
     * Proxy all method calls from $proxy to $replacement.
     *
     * @param mixed  $proxy       Проксируемый сервис.
     * @param object $replacement Мок сервиса.
     *
     * @return void
     */
    public static function swap($proxy, object $replacement): void
    {
        $definition = self::getDefinition($proxy);
        $definition->swap($replacement);

        // Initialize now so we can use it directly.
        self::addInitializer($proxy);
    }

    /**
     * Make the next call to $method name execute the $func.
     *
     * @param mixed    $proxy      Проксируемый сервис.
     * @param string   $methodName Метод.
     * @param callable ...$func    Callbacks.
     *
     * @return void
     */
    public static function next($proxy, string $methodName, callable ...$func): void
    {
        $definition = self::getDefinition($proxy);
        foreach ($func as $f) {
            $definition->appendMethodsQueue($methodName, $f);
        }

        // Initialize now so we can use it directly.
        self::addInitializer($proxy);
    }

    /**
     * All folloing calls $methodName will execute $func.
     *
     * @param mixed    $proxy      Проксируемый сервис.
     * @param string   $methodName Метод.
     * @param callable $func       Callback.
     *
     * @return void
     */
    public static function all($proxy, string $methodName, callable $func): void
    {
        $definition = self::getDefinition($proxy);
        $definition->addMethod($methodName, $func);

        // Initialize now so we can use it directly.
        self::addInitializer($proxy);
    }

    /**
     * Reset all services.
     *
     * @return void
     */
    public static function resetAll(): void
    {
        foreach (static::$definitions as $definition) {
            $definition->clear();
        }
    }

    /**
     * Reset this service.
     *
     * @param mixed $proxy Проксируемый сервис.
     *
     * @return void
     */
    public static function reset($proxy): void
    {
        $definition = self::getDefinition($proxy);
        $definition->clear();
    }

    /**
     * Remove all functions related to $methodName.
     *
     * @param mixed  $proxy      Проксируемый сервис.
     * @param string $methodName Метод.
     *
     * @return void
     */
    public static function resetMethod($proxy, string $methodName): void
    {
        $definition = self::getDefinition($proxy);
        $definition->removeMethod($methodName);
        $definition->clearMethodsQueue($methodName);
    }

    /**
     * This method is called in the proxy's constructor.
     *
     * @param LazyLoadingInterface $proxy Proxy.
     *
     * @return void
     *
     */
    public static function initializeProxy(LazyLoadingInterface $proxy): void
    {
        $definition = self::getDefinition($proxy);
        // Make sure the definition always have the latest original object.
        $definition->setOriginalObject($proxy->getWrappedValueHolderValue());
        self::addInitializer($proxy);
    }

    /**
     * @param LazyLoadingInterface $proxy Proxy.
     *
     * @return void
     */
    private static function addInitializer(LazyLoadingInterface $proxy): void
    {
        $initializer = function (&$wrappedObject, LazyLoadingInterface $proxy, $calledMethod, array $parameters, &$nextInitializer) {
            $nextInitializer = null;
            $wrappedObject = new Proxy(self::getDefinition($proxy));

            return true;
        };

        $proxy->setProxyInitializer($initializer);
    }

    /**
     * @param LazyLoadingInterface $proxy
     *
     * @return ProxyDefinition
     */
    private static function getDefinition($proxy): ProxyDefinition
    {
        if (!$proxy instanceof LazyLoadingInterface || !method_exists($proxy, 'getWrappedValueHolderValue')) {
            throw new \InvalidArgumentException(
                \sprintf('Object of class "%s" is not a proxy. Did you mark this service correctly?',
                    get_class($proxy))
            );
        }

        $key = sha1(get_class($proxy));
        if (!isset(self::$definitions[$key])) {
            self::$definitions[$key] = new ProxyDefinition($proxy->getWrappedValueHolderValue());
        }

        return self::$definitions[$key];
    }
}
