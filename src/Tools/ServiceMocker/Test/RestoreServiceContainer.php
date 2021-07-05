<?php

namespace Prokl\TestingTools\Tools\ServiceMocker\Test;

use Prokl\TestingTools\Tools\ServiceMocker\ServiceMock;

/**
 * After each test, make sure we restore the default behavior of all
 * services.
 *
 */
trait RestoreServiceContainer
{
    /**
     * @after
     */
    public static function _restoreContainer(): void
    {
        ServiceMock::resetAll();
    }
}
