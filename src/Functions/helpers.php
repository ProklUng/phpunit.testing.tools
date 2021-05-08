<?php

use Illuminate\Container\Container;
use Prokl\TestingTools\Tools\FixtureGenerator\FixtureManager;

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
