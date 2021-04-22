<?php

namespace Prokl\TestingTools\Traits\DataProviders;

use stdClass;
use Prokl\TestingTools\AbstractDataProvider;

class InvalidScalar extends AbstractDataProvider
{
    public function values()
    {
        return [
            'array' => $this->arrayOfStrings(),
            'null' => null,
            'object' => new stdClass(),
            'resource' => $this->resource(),
        ];
    }
}
