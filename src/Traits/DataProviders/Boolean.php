<?php

namespace Prokl\TestingTools\Traits\DataProviders;

use Prokl\TestingTools\AbstractDataProvider;

class Boolean extends AbstractDataProvider
{
    public function values()
    {
        return [
            'boolean-false' => false,
            'boolean-true' => true,
        ];
    }
}
