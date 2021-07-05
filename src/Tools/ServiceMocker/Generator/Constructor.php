<?php

declare(strict_types=1);

namespace Prokl\TestingTools\Tools\ServiceMocker\Generator;

use ReflectionException;
use function array_filter;
use function array_map;
use function implode;
use Laminas\Code\Generator\Exception\InvalidArgumentException;
use Laminas\Code\Generator\PropertyGenerator;
use Laminas\Code\Reflection\MethodReflection;
use Laminas\Code\Reflection\ParameterReflection;
use ProxyManager\Generator\MethodGenerator;
use ProxyManager\ProxyGenerator\Util\Properties;
use ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator;
use ReflectionClass;
use ReflectionMethod;
use function reset;
use function var_export;

/**
 * The `__construct` implementation for lazy loading proxies.
 *
 * @interal
 */
class Constructor extends MethodGenerator
{
    /**
     * @param ReflectionClass $originalClass
     * @param PropertyGenerator $valueHolder
     *
     * @return Constructor
     * @throws ReflectionException
     */
    public static function generateMethod(ReflectionClass $originalClass, PropertyGenerator $valueHolder): self
    {
        $originalConstructor = self::getConstructor($originalClass);

        $constructor = $originalConstructor
            ? self::fromReflectionWithoutBodyAndDocBlock($originalConstructor)
            : new self('__construct');

        $constructor->setBody(
            'static $reflection;'."\n\n"
            .'if (! $this->'.$valueHolder->getName().') {'."\n"
            .'    $reflection = $reflection ?? new \ReflectionClass('
            .var_export($originalClass->getName(), true)
            .");\n"
            .'    $this->'.$valueHolder->getName().' = $reflection->newInstanceWithoutConstructor();'."\n"
            .UnsetPropertiesGenerator::generateSnippet(Properties::fromReflectionClass($originalClass), 'this')
            .'}'
            .($originalConstructor ? self::generateOriginalConstructorCall($originalConstructor, $valueHolder) : '')
            ."\n"
            .'\Happyr\ServiceMocking\ServiceMock::initializeProxy($this);'
        );

        return $constructor;
    }

    /**
     * @param MethodReflection  $originalConstructor
     * @param PropertyGenerator $valueHolder
     *
     * @return string
     */
    private static function generateOriginalConstructorCall(
        MethodReflection $originalConstructor,
        PropertyGenerator $valueHolder
    ): string {
        return "\n\n"
            .'$this->'.$valueHolder->getName().'->'.$originalConstructor->getName().'('
            .implode(
                ', ',
                array_map(
                    static function (ParameterReflection $parameter): string {
                        return ($parameter->isVariadic() ? '...' : '').'$'.$parameter->getName();
                    },
                    $originalConstructor->getParameters()
                )
            )
            .');';
    }

    /**
     * @param ReflectionClass $class Класс.
     *
     * @return MethodReflection|null
     * @throws ReflectionException
     */
    private static function getConstructor(ReflectionClass $class): ?MethodReflection
    {
        $constructors = array_map(
            static function (ReflectionMethod $method): MethodReflection {
                return new MethodReflection(
                    $method->getDeclaringClass()->getName(),
                    $method->getName()
                );
            },
            array_filter(
                $class->getMethods(),
                static function (ReflectionMethod $method): bool {
                    return $method->isConstructor();
                }
            )
        );

        return reset($constructors) ?: null;
    }
}
