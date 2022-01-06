<?php

declare(strict_types=1);

namespace Clearfacts\Bundle\CodestyleBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CopyCsConfigCommand extends Command
{
    use FilesystemTrait;

    public const CONFIG_URL =
        'https://github.com/Clearfacts/cf-codestyle-bundle/raw/main/templates/cs/.php-cs';

    protected static $defaultName = 'clearfacts:codestyle:copy-cs-config';

    protected function configure(): void
    {
        $this
            ->setDescription('Copy latest code sniffing config')
            ->addOption('root', 'r', InputOption::VALUE_OPTIONAL, 'Root directory of the project', '.')
            ->addOption('config-dir', 'cd', InputOption::VALUE_OPTIONAL, 'Config directory of the project', 'config');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Preparing to copy cs config');

        $this->setup($input->getOption('root'), $input->getOption('config-dir'));

        $io->success('Cs config copied!');

        return 0;
    }

    private function setup(string $root, string $configDir): void
    {
        $modified = @filemtime($root . '/' . $configDir . '/.php-cs');
        if ($modified && (time() - $modified < 604800)) {
            return;
        }

        $contents = @file_get_contents(self::CONFIG_URL, false, stream_context_create([
            'http' => [
                'connect_timeout' => 2,
                'timeout' => 5,
            ],
        ]));
        if ($contents) {
            $this->getFileSystem()->dumpFile($root . '/' . $configDir . '/.php-cs', $contents);

            return;
        }

        /** @var SplFileInfo $file */
        foreach ($this->getFinder()->files()->in(__DIR__ . '/../../templates/cs')->name('.php-cs') as $file) {
            $configPath = $root . '/' . $configDir . '/' . $file->getFilename();
            $this->getFileSystem()->copy(
                $file->getRealPath(),
                $configPath
            );
        }
    }
}