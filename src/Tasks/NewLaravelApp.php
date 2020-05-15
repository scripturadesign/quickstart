<?php
declare(strict_types=1);

namespace Scriptura\QuickStart\Tasks;

use Scriptura\QuickStart\CommandRunner;
use Scriptura\QuickStart\ProjectFilesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewLaravelApp
{
    /**
     * @var bool
     */
    private $force;

    public function __construct(bool $force)
    {
        $this->force = $force;
    }

    public function __invoke(string $name, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info># Building Laravel application [' . $name . ']...</info>');

        $directory = getcwd();

        $commands = [
            'laravel new ' . $name . ' ' . ($this->force ? '--force' : ''),
        ];

        $runner = new CommandRunner($output, $directory);
        $isSuccessful = $runner->run($commands);

        $filesystem = new ProjectFilesystem(getcwd() . '/' . $name);

        $output->writeln('<info>Update file: .gitignore</info>');
        $filesystem->updateFile('.gitignore', function (string $content) {
            $patterns = [
                '/(\/node_modules)/',
            ];
            $replace = [
                "/.idea\n\${1}",
            ];

            return preg_replace($patterns, $replace, $content);
        });

        $output->writeln('<info>Update file: .env</info>');
        $filesystem->updateFile('.env', function (string $content) use ($name) {
            $patterns = [
                '/APP_URL=http:\/\/localhost/',
                '/DB_DATABASE=laravel/',
                '/DB_USERNAME=root/',
                '/DB_PASSWORD=/',
            ];
            $replace = [
                "APP_URL=http://{$name}.test",
                "DB_DATABASE={$name}",
                "DB_USERNAME=homestead",
                "DB_PASSWORD=secret",
            ];

            return preg_replace($patterns, $replace, $content);
        });

        $output->writeln('<info>Update file: .env.example</info>');
        $filesystem->updateFile('.env.example', function (string $content) use ($name) {
            $patterns = [
                '/APP_URL=http:\/\/localhost/',
                '/DB_DATABASE=laravel/',
                '/DB_USERNAME=root/',
                '/DB_PASSWORD=/',
            ];
            $replace = [
                "APP_URL=http://{$name}.test",
                "DB_DATABASE={$name}",
                "DB_USERNAME=homestead",
                "DB_PASSWORD=secret",
            ];

            return preg_replace($patterns, $replace, $content);
        });

        if ($isSuccessful) {
            $output->writeln('<comment>Default Laravel install done.</comment>');
        }
    }

}
