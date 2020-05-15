<?php

namespace Scriptura\QuickStart\Console;

use RuntimeException;
use Scriptura\QuickStart\Tasks\GitInit;
use Symfony\Component\Console\Command\Command;
use Scriptura\QuickStart\Tasks\GitCommit;
use Scriptura\QuickStart\Tasks\CodeCleanup;
use Symfony\Component\Console\Input\InputArgument;
use Scriptura\QuickStart\Tasks\PublishStubs;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Scriptura\QuickStart\Tasks\NewLaravelApp;
use Symfony\Component\Console\Output\OutputInterface;
use Scriptura\QuickStart\Tasks\InstallIdeHelper;
use Scriptura\QuickStart\Tasks\InstallTelescope;
use Scriptura\QuickStart\Tasks\InstallCodeSniffer;
use Scriptura\QuickStart\Tasks\InstallTailwindFrontendPreset;

class LaravelCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('laravel')
            ->setDescription('Quickstart a new Laravel application')
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Forces install even if the directory already exists');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $name = $input->getArgument('name');

        $directory = getcwd();

        if (! $input->getOption('force')) {
            $this->verifyApplicationDoesntExist($directory);
        }

        $tasks = [
            new NewLaravelApp((bool) $input->getOption('force')),
            new GitInit(),
            new GitCommit('Initial Laravel install'),
            new InstallTelescope(),
            new GitCommit('Installed Telescope'),
            new InstallIdeHelper(),
            new GitCommit('Installed Ide Helper'),
            new InstallTailwindFrontendPreset(),
            new GitCommit('Tailwindcss frontend preset'),
            new PublishStubs(),
            new GitCommit('Published stubs'),
            new CodeCleanup(),
            new GitCommit('Code cleanup'),
            new InstallCodeSniffer(),
            new GitCommit('Installed Code Sniffer'),
        ];

        foreach ($tasks as $task) {
            $task($name, $input, $output);
        }

        return 0;
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param  string  $directory
     * @return void
     */
    protected function verifyApplicationDoesntExist($directory)
    {
        if ((is_dir($directory) || is_file($directory)) && $directory != getcwd()) {
            throw new RuntimeException('Application already exists!');
        }
    }
}
