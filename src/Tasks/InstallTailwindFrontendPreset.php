<?php
declare(strict_types=1);

namespace Scriptura\QuickStart\Tasks;

use Scriptura\QuickStart\CommandRunner;
use Scriptura\QuickStart\ProjectFilesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallTailwindFrontendPreset
{
    public function __construct()
    {
    }

    public function __invoke(string $name, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info># Installing Tailwindcss frontend preset...</info>');

        $directory = getcwd() . '/' . $name;

        $composer = $this->findComposer();

        $commands = [
            $composer . ' require --dev laravel-frontend-presets/tailwindcss',
            'php artisan ui tailwindcss --auth',
            'yarn add tailwindcss --dev',
            'yarn add vue-template-compiler --dev',
            'yarn',
            'yarn dev',
        ];

        $runner = new CommandRunner($output, $directory);
        $isSuccessful = $runner->run($commands);

        $filesystem = new ProjectFilesystem(getcwd() . '/' . $name);

        $output->writeln('<info>Update file: resources/lang/en/pagination.php</info>');
        $filesystem->updateFile('resources/lang/en/pagination.php', function (string $content) {
            $patterns = [
                '/(\'next\' => \'Next &raquo;\',\n)/',
            ];
            $replace = [
                "\${1}    'goto_page' => 'Goto page #:page',\n",
            ];

            return preg_replace($patterns, $replace, $content);
        });

        $output->writeln('<info>Update file: .gitignore</info>');
        $filesystem->updateFile('.gitignore', function (string $content) {
            $patterns = [
                '/(\/public\/storage\n)/',
            ];
            $replace = [
                "\${1}/public/css\n/public/js\n/public/mix-manifest.json\n",
            ];

            return preg_replace($patterns, $replace, $content);
        });

        if ($isSuccessful) {
            $output->writeln('<comment>Tailwindcss preset installed.</comment>');
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
