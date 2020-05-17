<?php

declare(strict_types=1);

namespace Scriptura\QuickStart\Tasks;

use Scriptura\QuickStart\CommandRunner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GitCommit
{
    /**
     * @var string
     */
    private $commitMessage;

    public function __construct(string $commitMessage)
    {
        $this->commitMessage = $commitMessage;
    }

    public function __invoke(string $name, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info># New git commit [' . $this->commitMessage . ']...</info>');

        $directory = getcwd() . '/' . $name;

        $commands = [
            'git add .',
            'git commit -m "' . $this->commitMessage . '"',
        ];

        $runner = new CommandRunner($output, $directory);
        $isSuccessful = $runner->run($commands);

        if ($isSuccessful) {
            $output->writeln('<comment>Successully committed.</comment>');
        }
    }
}
