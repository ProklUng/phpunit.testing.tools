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

    /**
     * Относительная дата.
     *
     * @return Generator
     */
    public function provideDateTimeRelativeFormat(): ?Generator
    {
        yield['now'];
        yield['yesterday'];
        yield['tomorrow'];
        yield['back of 10'];
        yield['front of 10'];
        yield['last day of February'];
        yield['first day of next month'];
        yield['last day of previous month'];
        yield['last day of next month'];
        yield['Y-m-d'];
        yield['Y-m-d 10:00'];
    }

    /**
     * Путь к несуществующему файлу.
     *
     * @return Generator
     */
    public function provideNotExistingFilePath(): ?Generator
    {
        yield['lets-test.doc'];
        yield['lorem/ipsum.jpg'];
        yield['surprise/me/one/more/time.txt'];
    }
}