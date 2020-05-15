<?php
declare(strict_types=1);

namespace Scriptura\QuickStart\Tasks;

use Scriptura\QuickStart\CommandRunner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PublishStubs
{
    public function __construct()
    {
    }

    public function __invoke(string $name, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info># Publishing stubs...</info>');

        $directory = getcwd() . '/' . $name;

        $commands = [
            'php artisan stub:publish',
        ];

        $runner = new CommandRunner($output, $directory);
        $isSuccessful = $runner->run($commands);

        if ($isSuccessful) {
            $output->writeln('<comment>Stubs published.</comment>');
        }
    }
}
