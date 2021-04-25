<?php

namespace Prokl\TestingTools\Tools;

use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use ReflectionProperty;

/**
 * Trait PHPUnitTrait
 * Утилиты для тестирования. Исходит из убеждения, что тестовый объект называется obTestObject.
 * @package Prokl\TestingTools\Tools
 */
trait PHPUnitTrait
{
    /**
     * Мок интерфейса.
     *
     * @param string $className   Имя класса.
     * @param string $method      Метод.
     * @param mixed  $returnValue Возвращаемое значение.
     *
     * @return MockObject
     */
    public function mockInterface(
        string $className,
        string $method,
        $returnValue
    ): MockObject {
        /** @var MockObject $mockInterface */
        $mockInterface = $this->createMock($className);
        $mockInterface->method($method)->willReturn($returnValue);

        return $mockInterface;
    }

    /**
     * Мок интерфейса, ничего не возвращающего.
     *
     * @param string $className Имя класса.
     * @param string $method    Метод.
     *
     * @return MockObject
     */
    public function mockInterfaceVoid(
        string $className,
        string $method
    ): MockObject {
        /** @var MockObject $mockInterface */
        $mockInterface = $this->createMock($className);
        $mockInterface->method($method);

        return $mockInterface;
    }

    /**
     * Мок абстрактного класса.
     *
     * @param string $classname   Класс.
     * @param array  $arArguments Аргументы конструктора.
     *
     * @return mixed
     */
    public function mockAbstractClass(string $classname, array $arArguments = [])
    {
        return $this
            ->getMockBuilder($classname)
            ->setConstructorArgs($arArguments)
            ->getMockForAbstractClass();
    }

    /**
     * assertSame защищенного свойства.
     *
     * @param string $prop     Название переменной.
     * @param mixed  $expected Ожидаемое значение.
     * @param string $message  Ответ.
     *
     * @throws ReflectionException
     */
    protected function assertSameProtectedProp(
        string $prop,
        $expected,
        string $message = ''
    ): void {
        $result = PHPUnitUtils::getProtectedProperty(
            $this->obTestObject,
            $prop
        );

        $this->assertSame(
            $expected,
            $result,
            $message ?: $prop.' не тот, что ожидался. '
        );
    }

    /**
     * assertIsNumeric защищенного свойства.
     *
     * @param string $prop    Название переменной.
     * @param string $message Ответ.
     *
     * @throws ReflectionException
     */
    protected function assertIsNumericProtectedProp(
        string $prop,
        string $message = ''
    ): void {
        $result = PHPUnitUtils::getProtectedProperty(
            $this->obTestObject,
            $prop
        );

        $this->assertIsNumeric(
            $result,
            $message ?: $prop.' не тот, что ожидался. '
        );
    }

    /**
     * assertNotSame защищенного свойства.
     *
     * @param string $prop     Название переменной.
     * @param mixed  $expected Ожидаемое значение.
     * @param string $message  Ответ.
     *
     * @throws ReflectionException
     */
    protected function assertNotSameProtectedProp(
        string $prop,
        $expected,
        string $message = ''
    ): void {
        $result = PHPUnitUtils::getProtectedProperty(
            $this->obTestObject,
            $prop
        );

        $this->assertNotSame(
            $expected,
            $result,
            $message ?: $prop.' не тот, что ожидался.'
        );
    }

    /**
     * Очистить приватное статическое свойства.
     *
     * @param mixed  $className Название класса.
     * @param string $property  Свойство.
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function clearStaticProperty($className, string $property): void
    {
        $reflection = new ReflectionProperty($className, $property);
        $reflection->setAccessible(true);

        $reflection->setValue(null, null);
    }

    /**
     * assertEmpty защищенного свойства.
     *
     * @param string $prop Название переменной.
     *
     * @param string $message
     * @throws ReflectionException
     */
    protected function assertEmptyProtectedProp(string $prop, string $message = ''): void
    {
        $result = PHPUnitUtils::getProtectedProperty(
            $this->obTestObject,
            $prop
        );

        $this->assertEmpty(
            $result,
            $message
        );
    }

    /**
     * assertEmpty защищенного свойства. Ключ массива
     *
     * @param string $prop    Название переменной.
     * @param string $key     Ключ массива для проверки.
     * @param string $message Сообщение.
     *
     * @throws ReflectionException
     */
    protected function assertEmptyKeyProtectedProp(
        string $prop,
        string $key,
        string $message = ''
    ): void {
        $result = PHPUnitUtils::getProtectedProperty(
            $this->obTestObject,
            $prop
        );

        $this->assertEmpty(
            $result[$key],
            $message
        );
    }

    /**
     * assertEmpty защищенного свойства. Ключ массива
     *
     * @param string $prop    Название переменной.
     * @param string $key     Ключ массива для проверки.
     * @param string $message Сообщение.
     *
     * @throws ReflectionException
     */
    protected function assertNotEmptyKeyProtectedProp(
        string $prop,
        string $key,
        string $message = ''
    ): void {
        $result = PHPUnitUtils::getProtectedProperty(
            $this->obTestObject,
            $prop
        );

        $this->assertNotEmpty(
            $result[$key],
            $message
        );
    }

    /**
     * assertNotEmpty защищенного свойства.
     *
     * @param string $prop    Название переменной.
     * @param string $message Ответ.
     *
     * @return void
     * @throws ReflectionException
     */
    protected function assertNotEmptyProtectedProp(string $prop, string $message = ''): void
    {
        $result = PHPUnitUtils::getProtectedProperty(
            $this->obTestObject,
            $prop
        );

        $this->assertNotEmpty(
            $result,
            $message
        );
    }

    /**
     * assertNull защищенного свойства.
     *
     * @param string $prop    Название переменной.
     * @param string $message Ответ.
     *
     * @return void
     *
     * @throws ReflectionException
     */
    protected function assertNullProtectedProp(string $prop, string $message = ''): void
    {
        $result = PHPUnitUtils::getProtectedProperty(
            $this->obTestObject,
            $prop
        );

        $this->assertNull(
            $result,
            $message
        );
    }

    /**
     * assertTrue защищенного свойства.
     *
     * @param string $prop    Название переменной.
     * @param string $message Ответ.
     *
     * @return void
     * @throws ReflectionException
     */
    protected function assertTrueProtectedProp(string $prop, string $message = ''): void
    {
        $result = PHPUnitUtils::getProtectedProperty(
            $this->obTestObject,
            $prop
        );

        $this->assertTrue(
            $result,
            $message
        );
    }

    /**
     * assertFalse защищенного свойства.
     *
     * @param string $prop    Название переменной.
     * @param string $message Ответ.
     *
     * @return void
     * @throws ReflectionException
     */
    protected function assertFalseProtectedProp(string $prop, string $message = ''): void
    {
        $result = PHPUnitUtils::getProtectedProperty(
            $this->obTestObject,
            $prop
        );

        $this->assertFalse(
            $result,
            $message
        );
    }

    /**
     * assertNotNull защищенного свойства.
     *
     * @param string $prop    Название переменной.
     * @param string $message Ответ.
     *
     * @return void
     * @throws ReflectionException
     */
    protected function assertNotNullProtectedProp(string $prop, string $message = ''): void
    {
        $result = PHPUnitUtils::getProtectedProperty(
            $this->obTestObject,
            $prop
        );

        $this->assertNotNull(
            $result,
            $message
        );
    }
}
