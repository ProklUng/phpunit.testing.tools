<?php

namespace Prokl\TestingTools\Tests\Fixtures;

/**
 * Class StatefulService
 * @package Prokl\TestingTools\Tests\Fixtures
 */
class StatefulService
{
    private $data;

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }
}