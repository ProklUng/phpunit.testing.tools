<?php

namespace Prokl\TestingTools\Base;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
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
     * @param Command $commandInstance
     * @param string $commandName
     * @param array $params
     *
     * @return string
     */
    public function executeCommand(Command $commandInstance, string $commandName, array $params = [])
    {
        $application = new Application();
        $application->add($commandInstance);

        $command = $application->find($commandName);
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()) + $params);

        return $commandTester->getDisplay();
    }
}