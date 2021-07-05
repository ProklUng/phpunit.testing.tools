<?php

namespace Prokl\TestingTools\Tests\Cases\ServiceMocker;

use PHPUnit\Framework\TestCase;
use Prokl\TestingTools\Tools\ServiceMocker\Proxy\ProxyDefinition;

/**
 * Class ProxyDefinitionTest
 * @package Prokl\TestingTools\Tests\Cases\ServiceMocker
 */
class ProxyDefinitionTest extends TestCase
{
    /**
     * swap().
     *
     * @return void
     */
    public function testSwap() : void
    {
        $a = new \stdClass();
        $proxy = new ProxyDefinition($a);
        $this->assertSame($a, $proxy->getObject());

        $b = new \stdClass();
        $proxy->swap($b);
        $this->assertSame($b, $proxy->getObject());

        $proxy->clear();
        $this->assertSame($a, $proxy->getObject());
    }
}