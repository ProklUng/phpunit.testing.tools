<?php

namespace Prokl\TestingTools\Tools\Console;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * @since 14.09.2021
 */
trait InteractsWithConsole
{
    /**
     * @var Application|null $cliApplication Application CLI.
     */
    protected $cliApplication;

    /**
     * @param string $command Название команды.
     * @param array  $inputs  Входящие параметры.
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
     * @param string $command Название команды.
     *
     * @return TestCommand
     * @throws LogicException Когда нет контейнера или ядра.
     */
    final protected function consoleCommand(string $command): TestCommand
    {
        if (self::$kernel === null) {
            throw new LogicException('Cannot work without container & kernel');
        }

        $application = $this->cliApplication ?? new Application(self::$kernel);

        return TestCommand::from($application, $command);
    }
}
