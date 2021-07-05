<?php

namespace Prokl\TestingTools\Tests\Fixtures;

/**
 * Class ExampleService
 * @package Prokl\TestingTools\Tests\Fixtures
 */
class ExampleService
{
    public function getNumber(int $input = 0): int
    {
        return 4711 + $input;
    }
}