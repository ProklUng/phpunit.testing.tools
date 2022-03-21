<?php

use Illuminate\Container\Container;
use Prokl\TestingTools\Tools\FixtureGenerator\FixtureManager;
use Prokl\TestingTools\Tools\Invader;

if (!function_exists('containerLaravel')) {
    /**
     * Экземпляр сервис-контейнера Laravel.
     *
     * @return mixed
     */
    function containerLaravel()
    {
        return Container::getInstance();
    }
}

if (!function_exists('fixture')) {
    function fixture()
    {
        return containerLaravel()->make(FixtureManager::class);
    }
}

if (!function_exists('faker')) {
    function faker()
    {
        return FixtureManager::getFaker();
    }
}

if (! function_exists('invade')) {
    function invade(object $object)
    {
        return new Invader($object);
    }
}
