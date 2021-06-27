<?php

namespace Prokl\TestingTools\Traits;

use Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * Trait CommandTesterTrait
 * @package Prokl\TestingTools\Traits
 *
 * @since 27.06.2021
 */
trait CommandTesterTrait
{
    /**
     * Запуск команды.
     *
     * @param string $classCommand Класс команды.
     * @param array  $args         Аргументы команды.
     *
     * @return string То, что выводит команда.
     * @throws Exception
     */
    protected function runCommand(string $classCommand, array $args = []) : string
    {
        $application = new Application();
        $application->setAutoExit(false);

        $testCommand = new $classCommand;
        $application->add($testCommand);

        $command = array_merge(['command' => $testCommand->getName()], $args);

        $fp = tmpfile();
        $input = new ArrayInput($command);
        $output = new StreamOutput($fp);

        $application->run(
            $input,
            $output
        );

        fseek($fp, 0);
        $output = '';
        while (!feof($fp)) {
            $output = $output.fread($fp, 4096);
        }
        fclose($fp);

        return $output;
    }
}