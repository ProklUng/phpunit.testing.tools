<?php

namespace Prokl\TestingTools\Tools\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TestCommand
{
    /**
     * @var Command $command
     */
    private $command;

    /**
     * @var string $cli
     */
    private $cli;

    /**
     * @var array $inputs
     */
    private $inputs = [];

    /**
     * @var bool $splitOutputStreams
     */
    private $splitOutputStreams = false;

    /**
     * @param Command $command
     * @param string  $cli
     */
    private function __construct(Command $command, string $cli)
    {
        if (!$command->getApplication()) {
            $command->setApplication(new Application());
        }

        $this->command = $command;
        $this->cli = $cli;
    }

    /**
     * @param Command $command
     *
     * @return static
     */
    public static function for(Command $command): self
    {
        return new self($command, $command->getName());
    }

    /**
     * @param Application $application
     * @param string      $cli
     *
     * @return static
     */
    public static function from(Application $application, string $cli): self
    {
        foreach ($application->all() as $name => $commandObject) {
            if ($cli === \get_class($commandObject)) {
                return self::for($commandObject);
            }
        }

        return new self($application->find(\explode(' ', $cli, 2)[0]), $cli);
    }

    /**
     * @return $this
     */
    public function splitOutputStreams(): self
    {
        $this->splitOutputStreams = true;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function addArgument(string $value): self
    {
        $this->cli .= \sprintf(' "%s"', $value);

        return $this;
    }

    /**
     * @param string|array|null $value
     */
    public function addOption(string $name, $value = null): self
    {
        $name = 0 !== \mb_strpos($name, '-') ? "--{$name}" : $name;
        $value = $value ?? [null];

        foreach ((array) $value as $item) {
            $this->cli .= " {$name}";

            if ($item) {
                $this->cli .= \sprintf('="%s"', $item);
            }
        }

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function addInput(string $value): self
    {
        $this->inputs[] = $value;

        return $this;
    }

    /**
     * @param array $inputs
     *
     * @return $this
     */
    public function withInput(array $inputs): self
    {
        $this->inputs = $inputs;

        return $this;
    }

    /**
     * @return CommandResult
     */
    public function execute(): CommandResult
    {
        $tester = new CommandTester($this->command, new StringInput($this->cli));
        $tester->setInputs($this->inputs);

        return $tester->execute($this->splitOutputStreams);
    }
}
