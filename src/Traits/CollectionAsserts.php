<?php

namespace Prokl\TestingTools\Traits;

use Illuminate\Support\Collection;

/**
 * Trait CollectionAsserts
 * @package Prokl\TestingTools\
 *
 * @since 16.09.2020
 *
 * @see https://github.com/dmitry-ivanov/laravel-testing-tools
 */
trait CollectionAsserts
{
    /**
     * Assert that the given collections are equal based on the specified key.
     *
     * @param Collection $collection1 Коллекция 1.
     * @param Collection $collection2 Коллекция 2.
     * @param string     $key         Ключ.
     *
     * @return void
     */
    protected function assertCollectionsEqual(Collection $collection1, Collection $collection2, string $key): void
    {
        $this->assertEquals(
            $collection1->pluck($key)->sort()->values(),
            $collection2->pluck($key)->sort()->values(),
            'Failed asserting that collections are equal.'
        );
    }

    /**
     * Assert that the given collections are not equal based on the specified key.
     *
     * @param Collection $collection1 Коллекция 1.
     * @param Collection $collection2 Коллекция 2.
     * @param string     $key         Ключ.
     *
     * @return void
     */
    protected function assertCollectionsNotEqual(Collection $collection1, Collection $collection2, string $key): void
    {
        $this->assertNotEquals(
            $collection1->pluck($key)->sort()->values(),
            $collection2->pluck($key)->sort()->values(),
            'Failed asserting that collections are not equal.'
        );
    }
}
