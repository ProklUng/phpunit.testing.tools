<?php

namespace Prokl\TestingTools\Traits;

use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;

/**
 * Trait DataProvidersTrait
 * @package Prokl\TestingTools\Traits
 *
 * @since 14.10.2020 Переработка для придания работоспособности.
 */
trait DataProvidersTrait
{
    /**
     * @param string $locale
     *
     * @return Generator
     */
    protected function getFaker($locale = 'en_US'): Generator
    {
        static $fakers = [];

        if (!is_string($locale)) {
            throw new InvalidArgumentException('Locale should be a string');
        }

        if (!array_key_exists($locale, $fakers)) {
            $faker = Factory::create($locale);
            $faker->seed(9000);

            $fakers[$locale] = $faker;
        }

        return $fakers[$locale];
    }

    /**
     * @param array $values
     *
     * @return mixed
     */
    protected function provideData(array $values)
    {
        $result = [];

        foreach ($values as $key => $value) {
            $result[$key] = is_object($value) ? [$value->values()] : [$value];
        }

        return $result;
    }

    /**
     * @param mixed ...$dataProviders
     *
     * @return mixed
     */
    protected function provideDataFrom($dataProviders)
    {
        $values = array_reduce(
            $dataProviders,
            static function (array $carry, $dataProvider) {
                return array_merge(
                    $carry,
                    is_object($dataProvider) ? $dataProvider->values() : [$dataProvider]
                );
            },
            []
        );

        return $this->provideData($values);
    }

    /**
     * @param mixed ...$dataProviders
     *
     * @return array
     */
    protected function provideCombinedDataFrom(
        ...$dataProviders
    ): array {
        /**
         * @link https://stackoverflow.com/a/15973172
         */
        $values = [[]];

        foreach ($dataProviders as $key => $provider) {
            $append = [];

            foreach ($values as $product) {
                foreach ($provider->values() as $item) {
                    $product[$key] = $item;
                    $append[] = $product;
                }
            }

            $values = $append;
        }

        return $values;
    }
}
