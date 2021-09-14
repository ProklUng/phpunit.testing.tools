<?php

namespace Prokl\TestingTools\Tools\Console;

use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait InteractsWithConsole
{
    /**
     * @param string $command
     * @param array  $inputs
     *
     * @return CommandResult
     */
    final protected function executeConsoleCommand(string $command, array $inputs = []): CommandResult
    {
        return $this->consoleCommand($command)
            ->withInput($inputs)
            ->execute()
        ;
    }

    /**
     * @param string $command
     *
     * @return TestCommand
     */
    final protected function consoleCommand(string $command): TestCommand
    {
        if (self::$kernel === null) {
            throw new \LogicException('Cannot work without container & kernel');
        }

        return TestCommand::from(new Application(self::$kernel), $command);
    }
}
