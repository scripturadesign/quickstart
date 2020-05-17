<?php

declare(strict_types=1);

namespace Scriptura\QuickStart\Tasks;

use Scriptura\QuickStart\ProjectFilesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CodeCleanup
{
    public function __construct()
    {
    }

    public function __invoke(string $name, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info># Cleaning up some code...</info>');

        $filesystem = new ProjectFilesystem(getcwd() . '/' . $name);

        $output->writeln('<info>Update file: app/User.php</info>');
        $filesystem->updateFile('app/User.php', function (string $content) {
            $uses = <<<STRING
use Illuminate\\\\Auth\\\\Authenticatable;
use Illuminate\\\\Auth\\\\MustVerifyEmail;
use Illuminate\\\\Database\\\\Eloquent\\\\Model;
use Illuminate\\\\Notifications\\\\Notifiable;
use Illuminate\\\\Auth\\\\Passwords\\\\CanResetPassword;
use Illuminate\\\\Foundation\\\\Auth\\\\Access\\\\Authorizable;
use Illuminate\\\\Database\\\\Eloquent\\\\Builder as EloquentBuilder;
use Illuminate\\\\Contracts\\\\Auth\\\\Authenticatable as AuthenticatableContract;
use Illuminate\\\\Contracts\\\\Auth\\\\Access\\\\Authorizable as AuthorizableContract;
use Illuminate\\\\Contracts\\\\Auth\\\\CanResetPassword as CanResetPasswordContract;

STRING;

            $definition = <<<STRING
class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use MustVerifyEmail;

STRING;
            $patterns = [
                '/use Illuminate\\\\Contracts\\\\Auth\\\\MustVerifyEmail;\n/',
                '/use Illuminate\\\\Notifications\\\\Notifiable;\n/',
                '/use Illuminate\\\\Foundation\\\\Auth\\\\User as Authenticatable;/',
                '/class User extends Authenticatable\n{\n/',
            ];
            $replace = [
                '',
                '',
                $uses,
                $definition,
            ];

            return preg_replace($patterns, $replace, $content);
        });

        $redirectFunc = <<<STRING
public function redirectTo() : string
    {
        return RouteServiceProvider::HOME;
    }
STRING;

        $output->writeln('<info>Update file: app/Http/Controllers/Auth/ConfirmPasswordController.php</info>');
        $filesystem->updateFile('app/Http/Controllers/Auth/ConfirmPasswordController.php', function (string $content) use ($redirectFunc) {
            $patterns = [
                '/(@var)( string\s*\*\/\s*)(protected \$redirectTo = RouteServiceProvider::HOME;)/',
            ];
            $replace = [
                '@return${2}' . $redirectFunc,
            ];

            return preg_replace($patterns, $replace, $content);
        });

        $output->writeln('<info>Update file: app/Http/Controllers/Auth/LoginController.php</info>');
        $filesystem->updateFile('app/Http/Controllers/Auth/LoginController.php', function (string $content) use ($redirectFunc) {
            $patterns = [
                '/(@var)( string\s*\*\/\s*)(protected \$redirectTo = RouteServiceProvider::HOME;)/',
            ];
            $replace = [
                '@return${2}' . $redirectFunc,
            ];

            return preg_replace($patterns, $replace, $content);
        });

        $output->writeln('<info>Update file: app/Http/Controllers/Auth/RegisterController.php</info>');
        $filesystem->updateFile('app/Http/Controllers/Auth/RegisterController.php', function (string $content) use ($redirectFunc) {
            $patterns = [
                '/(@var)( string\s*\*\/\s*)(protected \$redirectTo = RouteServiceProvider::HOME;)/',
            ];
            $replace = [
                '@return${2}' . $redirectFunc,
            ];

            return preg_replace($patterns, $replace, $content);
        });

        $output->writeln('<info>Update file: app/Http/Controllers/Auth/ResetPasswordController.php</info>');
        $filesystem->updateFile('app/Http/Controllers/Auth/ResetPasswordController.php', function (string $content) use ($redirectFunc) {
            $patterns = [
                '/(@var)( string\s*\*\/\s*)(protected \$redirectTo = RouteServiceProvider::HOME;)/',
            ];
            $replace = [
                '@return${2}' . $redirectFunc,
            ];

            return preg_replace($patterns, $replace, $content);
        });

        $output->writeln('<info>Update file: app/Http/Controllers/Auth/VerificationController.php</info>');
        $filesystem->updateFile('app/Http/Controllers/Auth/VerificationController.php', function (string $content) use ($redirectFunc) {
            $patterns = [
                '/(@var)( string\s*\*\/\s*)(protected \$redirectTo = RouteServiceProvider::HOME;)/',
            ];
            $replace = [
                '@return${2}' . $redirectFunc,
            ];

            return preg_replace($patterns, $replace, $content);
        });

        $output->writeln('<info>Update all *.php files</info>');
        $filesystem->updateAllFilesOfType('php', function (string $content) {
            $patterns = [
                '/\<\?php/',
            ];
            $replace = [
                "<?php\n\ndeclare(strict_types=1);",
            ];

            return preg_replace($patterns, $replace, $content);
        });

        $output->writeln('<info>Update all *.stub files</info>');
        $filesystem->updateAllFilesOfType('stub', function (string $content) {
            $patterns = [
                '/\<\?php/',
            ];
            $replace = [
                "<?php\n\ndeclare(strict_types=1);",
            ];

            return preg_replace($patterns, $replace, $content);
        });

        $output->writeln('<comment>Code cleanup done.</comment>');
    }
}
