<?php

namespace Prokl\TestingTools\Traits;

/**
 * Trait ExceptionAsserts
 * @package Prokl\TestingTools\Traits
 *
 * @since 16.09.2020
 *
 * @see https://github.com/dmitry-ivanov/laravel-testing-tools
 */
trait ExceptionAsserts
{
    /**
     * Add expectation that the given exception would be thrown.
     *
     * @param string $class
     * @param string $message
     * @param int $code
     *
     * @return void
     */
    protected function willSeeException(string $class, string $message = '', int $code = 0): void
    {
        $this->expectException($class);

        if ($message) {
            $this->expectExceptionMessage($message);
        }

        $this->expectExceptionCode($code);
    }
}
