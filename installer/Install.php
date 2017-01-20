<?php declare(strict_types = 1);

namespace ApiClients\Middleware\Installer;

use Composer\Factory;
use Composer\Json\JsonFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Install extends Command
{
    const COMMAND = 'install';

    const NS_VENDOR       = '__NS_VENDOR__';
    const NS_TESTS_VENDOR = '__NS_TESTS_VENDOR__';
    const NS_PROJECT      = '__NS_PROJECT__';
    const PACKAGE_NAME    = '__PACKAGE_NAME__';
    const AUTHOR          = '__AUTHOR__';
    const AUTHOR_NAME     = '__AUTHOR_NAME__';
    const AUTHOR_EMAIL    = '__AUTHOR_EMAIL__';

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        retry:
        $style = new SymfonyStyle( $input, $output );

        $style->title('Welcome to the IceHawk installer.');
        $style->section('Please answer the following questions.');

        $replacements = [];
        $replacements[self::NS_VENDOR]       = $style->ask('What is your vendor namespace?', 'MyVendor');
        $replacements[self::NS_TESTS_VENDOR] = $style->ask('What is your vendor test namespace?', 'MyVendor\\Tests');
        $replacements[self::NS_PROJECT]      = $style->ask('What is your project namespace?', 'MyProject');
        $replacements[self::PACKAGE_NAME]    = $style->ask(
            'What is your package name?',
            strtolower($replacements[self::NS_VENDOR]) . '/' . strtolower($replacements[self::NS_PROJECT])
        );
        $replacements[self::AUTHOR_NAME]  = $style->ask('What is your name?');
        $replacements[self::AUTHOR_EMAIL] = $style->ask('What is your email address?');

        while ( false === filter_var( $replacements[self::AUTHOR_EMAIL], FILTER_VALIDATE_EMAIL ) )
        {
            $replacements[self::AUTHOR_EMAIL] = $style->ask('Invalid email address, try again.');
        }

        $replacements[self::AUTHOR] = "{$replacements[self::AUTHOR_NAME]} <{$replacements[self::AUTHOR_EMAIL]}>";

        $style->section('Summary:');

        $style->table(
            [],
            [
                ['Your namespace', $replacements[self::NS_VENDOR] . '\\' . $replacements[self::NS_PROJECT]],
                ['Your test namespace', $replacements[self::NS_TESTS_VENDOR] . '\\' . $replacements[self::NS_PROJECT]],
                ['Your package', $replacements[self::PACKAGE_NAME]],
                ['Author name', $replacements[self::AUTHOR_NAME]],
                ['Author email', $replacements[self::AUTHOR_EMAIL]],
            ]
        );

        $installNow = $style->choice(
            'All settings correct?',
            ['Yes', 'Change settings', 'Cancel installation'],
            'Yes'
        );

        switch ( $installNow )
        {
            case 'Yes':
            {
                $style->text('Creating your middleware package now.');
                $style->section('Updating composer.json');
                $this->updateComposerJson($replacements);
                $style->text('Updated composer.json');
                $style->success('Your middleware package creation has been successfully.');

                break;
            }
            case 'Change settings':
            {
                goto retry;
                break;
            }
            case 'Cancel installation':
            {
                $style->error( 'Installation canceled.' );

                return 9;
            }
        }

        return 0;
    }

    private function updateComposerJson(array $replacements)
    {
        $json = new JsonFile(Factory::getComposerFile());
        $composerJson = $json->read();

        // Replace name
        $composerJson['name'] = $replacements[self::PACKAGE_NAME];

        // Replace authors
        $composerJson['authors'] = [
            [
                'name'  => $replacements[self::AUTHOR_NAME],
                'email' => $replacements[self::AUTHOR_EMAIL],
            ],
        ];

        // Add autoload entries
        $composerJson['autoload']['psr-4'][$replacements[self::NS_VENDOR] . '\\' . $replacements[self::NS_PROJECT]] = 'src/';
        $composerJson['autoload-dev']['psr-4'][$replacements[self::NS_TESTS_VENDOR] . '\\' . $replacements[self::NS_PROJECT]] = 'tests/';

        // Removed installer autoload, installer required package, and install command
        unset(
            $composerJson['autoload']['psr-4']['ApiClients\\Middleware\\Installer\\'],
            $composerJson['autoload']['psr-4']['ApiClients\\Middleware\\Skeleton\\'],
            $composerJson['autoload-dev']['psr-4']['ApiClients\\Tests\\Middleware\\Skeleton\\'],
            $composerJson['require']['composer/composer'],
            $composerJson['require']['nikic/php-parser'],
            $composerJson['require']['ocramius/package-versions'],
            $composerJson['require']['symfony/console'],
            $composerJson['scripts']['post-create-project-cmd']
        );

        $json->write($composerJson);
    }
}