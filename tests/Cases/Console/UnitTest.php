<?php

namespace Prokl\TestingTools\Tests\Cases\Console;

use PHPUnit\Framework\TestCase;
use Prokl\TestingTools\Tests\Cases\Console\Fixture\FixtureCommand;
use Prokl\TestingTools\Tests\Cases\Console\Fixture\Kernel;
use Prokl\TestingTools\Tools\Console\TestCommand;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UnitTest extends TestCase
{
    /**
     * @var Kernel $kernel
     */
    protected static $kernel;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        self::$kernel = new Kernel('dev', true);
        parent::setUp();
    }

    /**
     * @test
     */
    public function command_with_no_arguments(): void
    {
        TestCommand::for(new FixtureCommand())->execute()
            ->assertSuccessful()
            ->assertOutputContains('Executing command')
            ->assertOutputNotContains('arg1')
            ->assertOutputNotContains('opt1')
            ->assertOutputContains('Error output')
        ;
    }

    /**
     * @test
     */
    public function can_split_output_streams(): void
    {
        TestCommand::for(new FixtureCommand())
            ->splitOutputStreams()
            ->execute()
            ->assertSuccessful()
            ->assertOutputContains('Executing command')
            ->assertOutputNotContains('arg1')
            ->assertOutputNotContains('opt1')
            ->assertOutputNotContains('Error output')
            ->assertErrorOutputContains('Error output')
            ->assertErrorOutputNotContains('Executing command')
        ;
    }

    /**
     * @test
     */
    public function command_with_arguments_and_options(): void
    {
        TestCommand::for(new FixtureCommand())
            ->addArgument('value')
            ->addOption('opt1')
            ->addOption('--opt2', 'v1')
            ->addOption('--opt3', ['v2', 'v3'])
            ->addOption('--opt3', 'v4')
            ->execute()
            ->assertSuccessful()
            ->assertOutputContains('Executing command')
            ->assertOutputContains('arg1 value: value')
            ->assertOutputContains('opt1 option set')
            ->assertOutputContains('opt2 value: v1')
            ->assertOutputContains('opt3 value: v2')
            ->assertOutputContains('opt3 value: v3')
            ->assertOutputContains('opt3 value: v4')
        ;
    }

    /**
     * @test
     */
    public function can_add_input(): void
    {
        TestCommand::for(new FixtureCommand())
            ->addInput('foobar')
            ->execute()
            ->assertSuccessful()
            ->assertOutputContains('Executing command')
            ->assertOutputContains('arg1 value: foobar')
            ->assertOutputNotContains('opt1')
        ;
    }

    /**
     * @test
     */
    public function default_verbosity_and_decorated(): void
    {
        TestCommand::for(new FixtureCommand())
            ->execute()
            ->assertOutputContains('verbosity: 32')
            ->assertOutputContains('decorated: no')
        ;
    }

    /**
     * @test
     */
    public function can_decorate_with_ansi_option(): void
    {
        TestCommand::for(new FixtureCommand())
            ->addOption('--ansi')
            ->execute()
            ->assertOutputContains('decorated: yes')
        ;
    }

    /**
     * @test
     */
    public function can_adjust_verbosity_with_v_option(): void
    {
        TestCommand::for(new FixtureCommand())
            ->addOption('-vv')
            ->execute()
            ->assertOutputContains('verbosity: 128')
        ;
    }

    /**
     * @test
     */
    public function can_turn_off_interaction(): void
    {
        TestCommand::for(new FixtureCommand())
            ->addInput('kbond')
            ->addOption('-n')
            ->execute()
            ->assertOutputNotContains('arg1')
            ->assertOutputNotContains('kbond')
        ;
    }
}
