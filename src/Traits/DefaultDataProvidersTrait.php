<?php

namespace Prokl\TestingTools\Traits;

use DateTime;
use Generator;

/**
 * Trait DefaultDataProvidersTrait
 * @package Prokl\TestingTools\Traits
 *
 * @since 03.05.2021
 */
trait DefaultDataProvidersTrait
{
    /**
     * Пустые значения.
     *
     * @return Generator
     */
    public function provideEmptyValue(): ?Generator
    {
        yield[''];
        yield['   '];
        yield[null];
        yield[0];
        yield[false];
        yield[[]];
    }

    /**
     * Пустые скалярные значения.
     *
     * @return Generator
     */
    public function provideEmptyScalarValue(): ?Generator
    {
        yield[''];
        yield['   '];
        yield[null];
        yield[0];
        yield[false];
    }

    /**
     * Булевы значения.
     *
     * @return Generator
     */
    public function provideBooleanValue(): ?Generator
    {
        yield[false];
        yield[true];
    }

    /**
     * DateTime.
     *
     * @return Generator
     */
    public function provideDateTimeInstance(): ?Generator
    {
        yield[new DateTime()];
        yield[new DateTime('yesterday')];
        yield[new DateTime('now')];
        yield[new DateTime('tomorrow')];
    }
}