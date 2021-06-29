<?php

namespace Prokl\TestingTools\Base;

use Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class CommandTestCase
 * @package Prokl\TestingTools\Base
 *
 * @since 14.05.2021
 */
class CommandTestCase extends BaseTestCase
{
    /**
     * @param Command $commandInstance Команда.
     * @param string  $commandName     Название команды.
     * @param array   $params          Параметры.
     *
     * @return string
     */
    public function executeCommand(Command $commandInstance, string $commandName, array $params = [])
    {
        $application = new Application();
        $application->add($commandInstance);

        $command = $application->find($commandName);
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()] + $params);

        return $commandTester->getDisplay();
    }

    /**
     * @param Command $command Команда.
     * @param array   $input
     *
     * @return mixed
     * @throws Exception
     */
    protected function runCommand(Command $command, $input = [])
    {
        return $command->run(new ArrayInput($input), new NullOutput());
    }
}