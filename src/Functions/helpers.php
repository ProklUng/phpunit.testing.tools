<?php

use Illuminate\Container\Container;

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
