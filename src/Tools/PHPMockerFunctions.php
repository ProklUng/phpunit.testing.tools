<?php

namespace Prokl\TestingTools\Tools;

use phpmock\mockery\PHPMockery;

/**
 * Class PHPMockerFunctions
 * @package Prokl\TestingTools\Tools
 */
class PHPMockerFunctions
{
    /** @var array $mockedFunctions Подготовленные к моку функции. */
    private static $mockedFunctions = [];

    /** @var array $argsMockedFunctions Аргументы замоканных функций. */
    private static $argsMockedFunctions = [];

    /** @var array $readyMocks Готовые моки. */
    private static $readyMocks = [];

    /** @var string $namespace Пространство имен, где мокаются функции. */
    private $namespace;

    /**
     * PHPMockerFunctions constructor.
     *
     * @param string $namespace Пространство имен.
     */
    public function __construct(string $namespace = '')
    {
        $this->namespace = $namespace;
    }

    /**
     * Добавить.
     *
     * @param string $function    Функция.
     * @param mixed  $returnValue Возвращаемое значение.
     *
     * @return PHPMockerFunctions
     */
    public function full(
        string $function,
        $returnValue
    ) : self {
        self::$mockedFunctions[$function] = $returnValue;

        return $this;
    }

    /**
     * @param string $function    Функция.
     * @param mixed  $returnValue Возвращаемое значение.
     * @param mixed  $args        Аргументы.
     *
     * @return $this
     */
    public function partial(
        string $function,
        $returnValue,
        $args = null
    ) : self {
        self::$mockedFunctions[$function] = $returnValue;
        self::$argsMockedFunctions[$function] = $args;

        return $this;
    }

    /**
     * Мокинг.
     *
     * @return void
     */
    public function mock() : void
    {
        foreach (self::$mockedFunctions as $function => $returnValue) {
            if (!empty(self::$readyMocks[$function])) {
                continue;
            }

            if (empty(self::$argsMockedFunctions[$function])) {
                self::$readyMocks[$function] = $this->fullMockFunction(
                    $this->namespace,
                    $function,
                    $returnValue
                );

                continue;
            }

            self::$readyMocks[$function] = $this->mockFunction(
                $this->namespace,
                $function,
                $returnValue,
                self::$argsMockedFunctions[$function]
            );
        }
    }

    /**
     * Очистка.
     *
     * @return void
     */
    public function shutdown() : void
    {
        if (empty(self::$mockedFunctions)) {
            return;
        }

        $functions = array_keys(self::$mockedFunctions);

        $this->resetMockedFunctions(
            $this->namespace,
            ...$functions
        );

        self::$mockedFunctions = self::$argsMockedFunctions = [];
        self::$readyMocks = [];
    }

    /**
     * Задать пространство имен.
     *
     * @param string $namespace Пространство имен.
     *
     * @return $this
     */
    public function setNamespace(string $namespace): PHPMockerFunctions
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Готовый мок.
     *
     * @param string $function
     *
     * @return mixed|null
     */
    public function getMock(string $function)
    {
        return !empty(self::$readyMocks[$function]) ? self::$readyMocks[$function] : null;
    }

    /**
     *
     * Обнулить все замоканные функции.
     *
     * @param string $namespace    Пространство имен.
     * @param mixed  ...$functions Функции к обнулению.
     */
    private function resetMockedFunctions(
        string $namespace,
        ...$functions
    ) : void {
        foreach ($functions as $function) {
            PHPMockery::define($namespace, $function);
        }
    }

    /**
     * Полный мок функции.
     *
     * @param string $namespace   Пространство имен.
     * @param string $function    Функция.
     * @param mixed  $returnValue Возвращаемое значение.
     *
     * @return mixed
     */
    private function fullMockFunction(
        string $namespace,
        string $function,
        $returnValue
    ) {
        PHPMockery::define($namespace, $function);

        $mock = PHPMockery::mock(
            $namespace,
            $function
        );

        $mock->andReturn($returnValue);

        return $mock;
    }

    /**
     * Мок глобальной функции.
     *
     * @param string $namespace   Пространство имен.
     * @param string $function    Функция.
     * @param mixed  $returnValue Возвращаемое значение.
     * @param mixed  $args        Аргументы.
     *
     * @return mixed
     */
    private function mockFunction(
        string $namespace,
        string $function,
        $returnValue,
        $args = null
    ) {
        PHPMockery::define($namespace, $function);

        $mock = PHPMockery::mock(
            $namespace,
            $function
        );

        $mock->andReturnUsing(static function ($arg = null) use ($returnValue, $args, $function) {
            if ($arg === $args) {
                return $returnValue;
            }

            return $function($args);
        });

        return $mock;
    }
}
