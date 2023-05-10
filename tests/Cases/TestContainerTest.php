<?php

namespace Prokl\TestingTools\Tests\Cases;

use Exception;
use Mockery;
use Prokl\TestingTools\Base\BaseTestCase;
use Prokl\TestingTools\Tests\Fixtures\ExampleService;
use Prokl\TestingTools\Tools\Container\BuildContainer;
use Prokl\TestingTools\Tools\Container\CustomTestContainer;
use Prokl\TestingTools\Tools\Container\TestKernel;
use Prokl\TestingTools\Traits\BootTestKernelTrait;

/**
 * Class TestContainerTest
 * @package Prokl\TestingTools\Tests\Cases
 *
 * @since 06.07.2021
 */
class TestContainerTest extends BaseTestCase
{
    use BootTestKernelTrait;

    /**
     * @var CustomTestContainer $obTestObject
     */
    protected $obTestObject;

    /**
     * @var TestKernel $kernel Test Kernel.
     */
    protected static $kernel;

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->container = BuildContainer::getTestContainer(
            [
                'test_container.yaml'
            ],
            '/tests/Fixtures/'
        );

        $this::{$kernel} = $this->bootTestKernel($this->container);

        $this->obTestObject = new CustomTestContainer(
            $this::{$kernel},
            'double.service.container'
        );

        $this->obTestObject->setTestContainer($this->container);

        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this::{$kernel}->shutdown();
        $this->obTestObject->reset();
    }

    /**
     * Инициализация.
     *
     * @return void
     * @throws Exception
     */
    public function testInit() : void
    {
        $result = $this->obTestObject->get('test_service');

        $this->assertInstanceOf(ExampleService::class, $result);
        $this->assertSame(
            4711,
            $result->getNumber()
        );
    }

    /**
     * Наличие сервиса kernel.
     *
     * @return void
     * @throws Exception
     */
    public function testHasKernel() : void
    {
        $this->assertTrue(
            $this->obTestObject->has('kernel'),
            'Kernel не установился'
        );
    }

    /**
     * set(). Работает ли подмена сервисов.
     *
     * @return void
     * @throws Exception
     */
    public function testSwapService() : void
    {
        $mock = Mockery::mock(ExampleService::class);
        $mock = $mock->shouldReceive('getNumber')->andReturn(111);

        $this->obTestObject->set('test_service', $mock->getMock());

        $result = $this->obTestObject->get('test_service')->getNumber();

        $this->assertSame(
            111,
            $result
        );
    }

    /**
     * isCompiled().
     *
     * @return void
     * @throws Exception
     */
    public function testIsCompiled() : void
    {
        $result = $this->obTestObject->isCompiled();

        $this->assertTrue($result);
    }
}
