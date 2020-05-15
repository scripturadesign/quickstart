<?php
declare(strict_types=1);

namespace Scriptura\QuickStart\Tasks;

use Scriptura\QuickStart\CommandRunner;
use Scriptura\QuickStart\ProjectFilesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCodeSniffer
{
    public function __construct()
    {
    }

    public function __invoke(string $name, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info># Installing Code Sniffer...</info>');

        $directory = getcwd() . '/' . $name;

        $composer = $this->findComposer();

        $commands = [
            $composer . ' require --dev squizlabs/php_codesniffer',
        ];

        $runner = new CommandRunner($output, $directory);
        $isSuccessful = $runner->run($commands);

        $filesystem = new ProjectFilesystem(getcwd() . '/' . $name);

        $filesystem->createFile(
            'phpcs.xml.dist',
            <<<FILE
<?xml version="1.0"?>
<ruleset name="{$name}">
    <description>The coding standard of {$name}</description>

    <file>app</file>
    <file>config</file>
    <file>database</file>
    <file>routes</file>
    <file>tests</file>

    <arg value="p" />

    <config name="ignore_warnings_on_exit" value="1" />
    <config name="ignore_errors_on_exit" value="1" />
    <config name="php_version" value="70205" />

    <arg name="basepath" value="."/>
    <arg name="extensions" value="php" />
    <arg name="colors" />
    <arg value="s" />

    <!-- Use the PSR12 Standard-->
    <rule ref="PSR12">
        <exclude name="PSR1.Classes.ClassDeclaration.MissingNamespace"/>
    </rule>

    <!-- Ban some functions -->
    <rule ref="Generic.PHP.ForbiddenFunctions">
        <properties>
            <property name="forbiddenFunctions" type="array">
                <element key="sizeof" value="count"/>
                <element key="delete" value="unset"/>
                <element key="print" value="echo"/>
                <element key="is_null" value="null"/>
                <element key="create_function" value="null"/>
            </property>
        </properties>
    </rule>
</ruleset>
FILE
        );

        $output->writeln('<info>Update file: composer.json</info>');
        $filesystem->updateFile('composer.json', function (string $content) {
            $refreshScript = <<<STRING
        "test": "phpunit",
        "check-style": "phpcs",
        "fix-style": "phpcbf; if [ $? -eq 1 ]; then exit 0; fi",

STRING;
            $patterns = [
                '/("scripts": {\n)/',
            ];
            $replace = [
                '${1}' . $refreshScript,
            ];

            return preg_replace($patterns, $replace, $content);
        });


        $isSuccessful = $runner->run([
            $composer . ' fix-style',
            $composer . ' check-style',
        ]);

        if ($isSuccessful) {
            $output->writeln('<comment>Code Sniffer installed.</comment>');
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
