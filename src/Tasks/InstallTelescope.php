<?php
declare(strict_types=1);

namespace Scriptura\QuickStart\Tasks;

use Scriptura\QuickStart\CommandRunner;
use Scriptura\QuickStart\ProjectFilesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallTelescope
{
    public function __construct()
    {
    }

    public function __invoke(string $name, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info># Installing Telescope...</info>');

        $directory = getcwd() . '/' . $name;

        $composer = $this->findComposer();

        $commands = [
            $composer . ' require laravel/telescope',
            'php artisan telescope:install',
        ];

        $runner = new CommandRunner($output, $directory);
        $isSuccessful = $runner->run($commands);

        $filesystem = new ProjectFilesystem(getcwd() . '/' . $name);

        $output->writeln('<info>Update file: app/Console/Kernel.php</info>');
        $filesystem->updateFile('app/Console/Kernel.php', function (string $content) {
            $patterns = [
                '/\/\/ \$schedule/',
                '/inspire/',
                '/hourly/',
            ];
            $replace = [
                '$schedule',
                'telescope:prune --hours=24',
                'daily',
            ];

            return preg_replace($patterns, $replace, $content);
        });

        if ($isSuccessful) {
            $output->writeln('<comment>Telescope installed.</comment>');
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
