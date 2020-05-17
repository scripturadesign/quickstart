<?php

declare(strict_types=1);

namespace Scriptura\QuickStart;

use RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;

class CommandRunner
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $directory;

    public function __construct(OutputInterface $output, string $directory)
    {
        $this->output = $output;
        $this->directory = $directory;
    }

    public function run(array $commands): bool
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), $this->directory, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('Warning: ' . $e->getMessage());
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });

        return $process->isSuccessful();
    }
}
