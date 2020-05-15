<?php
declare(strict_types=1);

namespace Scriptura\QuickStart\Tasks;

use Scriptura\QuickStart\CommandRunner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GitInit
{
    public function __construct()
    {
    }

    public function __invoke(string $name, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info># Initialize git repository...</info>');

        $directory = getcwd() . '/' . $name;

        $composer = $this->findComposer();

        $commands = [
            'git init',
        ];

        $runner = new CommandRunner($output, $directory);
        $isSuccessful = $runner->run($commands);

        if ($isSuccessful) {
            $output->writeln('<comment>Git repository initialized.</comment>');
        }
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer() : string
    {
        $composerPath = getcwd() . '/composer.phar';

        if (file_exists($composerPath)) {
            return '"' . PHP_BINARY . '" ' . $composerPath;
        }

        return 'composer';
    }
}
