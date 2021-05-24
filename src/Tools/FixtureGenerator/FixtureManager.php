<?php

namespace Prokl\TestingTools\Tools\FixtureGenerator;

use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;

/**
 * Class FixtureManager
 * @package Prokl\TestingTools\Tools\FixtureGenerator
 */
class FixtureManager
{
    /**
     * @var string $folder
     */
    protected $folder;

    /**
     * @var $faker
     */
    protected static $faker;

    /**
     * FixtureManager constructor.
     *
     * @param string $folder Папка, откуда будут тянуться фикстуры.
     */
    public function __construct(string $folder)
    {
        $this->folder = $folder;
        if (!file_exists($folder)) {
            throw new InvalidArgumentException('No such folder: ' . $folder);
        }
    }

    /**
     * Returns a fixture
     *
     * @param   string $code       Code.
     * @param   array  $additional Default: []
     *
     * @return  array
     */
    public function make(string $code, array $additional = []): array
    {
        $code = str_replace('.', '/', $code);

        $path = $this->folder . '/' . $code . '.php';

        if (!file_exists($path)) {
            throw new InvalidArgumentException('No such fixture: ' . $path);
        }

        $fixture = @include $path;

        if (! is_array($fixture)) {
            throw new InvalidArgumentException('Fixture must be a valid array');
        }

        return array_merge($fixture, $additional);
    }

    /**
     * makeMany.
     *
     * @param   string  $code
     * @param   integer $quantity
     * @param   array   $additional Default: []
     *
     * @return  array
     */
    public function makeMany(string $code, int $quantity, array $additional = []): array
    {
        $data = [];

        while ($quantity > 0) {
            $data[] = $this->make($code, $additional);

            --$quantity;
        }

        return $data;
    }

    /**
     * getFaker.
     *
     * @return Generator
     */
    public static function getFaker(): Generator
    {
        if (empty(static::$faker)) {
            static::$faker = Factory::create();
        }

        return static::$faker;
    }
}
