<?php

declare(strict_types=1);

namespace Scriptura\QuickStart\Tasks;

use Scriptura\QuickStart\CommandRunner;
use Scriptura\QuickStart\ProjectFilesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallIdeHelper
{
    public function __construct()
    {
    }

    public function __invoke(string $name, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info># Installing Ide Helper...</info>');

        $directory = getcwd() . '/' . $name;

        $composer = $this->findComposer();

        $commands = [
            $composer . ' require --dev barryvdh/laravel-ide-helper doctrine/dbal',
            'php artisan vendor:publish --provider="Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" --tag=config',
        ];

        $runner = new CommandRunner($output, $directory);
        $runner->run($commands);

        $filesystem = new ProjectFilesystem(getcwd() . '/' . $name);

        $output->writeln('<info>Update file: composer.json</info>');
        $filesystem->updateFile('composer.json', function (string $content) {
            $refreshScript = <<<STRING
        "refresh": [
            "@php artisan migrate:fresh --ansi",
            "@php artisan db:seed --ansi",
            "@php artisan ide-helper:models -W -R --ansi"
        ],
        "post-update-cmd": [
            "Illuminate\\\\\\\\Foundation\\\\\\\\ComposerScripts::postUpdate",
            "php artisan ide-helper:generate",
            "php artisan ide-helper:meta"
        ],

STRING;
            $patterns = [
                '/("scripts": {\n)/',
            ];
            $replace = [
                '${1}' . $refreshScript,
            ];

            return preg_replace($patterns, $replace, $content);
        });

        $output->writeln('<info>Update file: config/ide-helper.php</info>');
        $filesystem->updateFile('config/ide-helper.php', function (string $content) {
            $patterns = [
                '/\'include_fluent\' => false,/',
                '/\'include_factory_builders\' => false,/',
                '/\'write_model_magic_where\' => true,/',
                '/\'write_eloquent_model_mixins\' => false,/',
            ];
            $replace = [
                '\'include_fluent\' => true,',
                '\'include_factory_builders\' => true,',
                '\'write_model_magic_where\' => false,',
                '\'write_eloquent_model_mixins\' => true,',
            ];

            return preg_replace($patterns, $replace, $content);
        });

        $isSuccessful = $runner->run([$composer . ' update']);

        if ($isSuccessful) {
            $output->writeln('<comment>Ide Helper installed.</comment>');
        }
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer(): string
    {
        $composerPath = getcwd() . '/composer.phar';

        if (file_exists($composerPath)) {
            return '"' . PHP_BINARY . '" ' . $composerPath;
        }

        return 'composer';
    }
}
