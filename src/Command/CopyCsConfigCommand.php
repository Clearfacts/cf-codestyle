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

    public const CS_CONFIG_URL = 'https://github.com/Clearfacts/cf-codestyle-bundle/raw/main/templates/cs/.php-cs-fixer.dist.php';
    public const LINT_CONFIG_URL = 'https://github.com/Clearfacts/eslint-config/raw/main/index.js';

    protected static $defaultName = 'clearfacts:codestyle:copy-cs-config';

    /**
     * @var SymfonyStyle
     */
    private $io;

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Copy latest code sniffing config')
            ->addOption('root', 'r', InputOption::VALUE_OPTIONAL, 'Root directory of the project', '.');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('Preparing to copy cs config');

        $root = $input->getOption('root');
        $this->setupCs($root);
        $this->setupLint($root);

        return 0;
    }

    private function setupCs(string $root): void
    {
        $phpcsConfig = $root . '/.php-cs-fixer.dist.php';
        $modified = @filemtime($phpcsConfig);
        if ($modified && (time() - $modified < 604800)) {
            $this->io->warning('Cs config already exists and is less than a week old');

            return;
        }

        $contents = @file_get_contents(self::CS_CONFIG_URL, false, stream_context_create([
            'http' => [
                'connect_timeout' => 2,
                'timeout' => 5,
            ],
        ]));
        if ($contents) {
            $this->getFileSystem()->dumpFile($phpcsConfig, $contents);
            $this->io->success('Copied cs config from ' . self::CS_CONFIG_URL);

            return;
        }

        /** @var SplFileInfo $file */
        foreach ($this->getFinder()->files()->ignoreDotFiles(false)->in(__DIR__ . '/../../templates/cs')->name('.php-cs-fixer.dist.php') as $file) {
            $configPath = $root . '/' . $file->getFilename();
            $this->getFileSystem()->copy(
                $file->getRealPath(),
                $configPath
            );
            $this->io->success('Copied cs config from vendor package');
        }
    }

    private function setupLint(string $root): void
    {
        $lintConfig = $root . '/.eslintrc';
        $modified = @filemtime($lintConfig);
        if ($modified && (time() - $modified < 604800)) {
            $this->io->warning('Lint config already exists and is less than a week old');

            return;
        }

        $contents = @file_get_contents(self::LINT_CONFIG_URL, false, stream_context_create([
            'http' => [
                'connect_timeout' => 2,
                'timeout' => 5,
            ],
        ]));
        if ($contents) {
            $contents = str_replace('module.exports = ', '', $contents);
            $this->getFileSystem()->dumpFile($lintConfig, $contents);
            $this->io->success('Copied lint config from ' . self::LINT_CONFIG_URL);

            return;
        }

        /** @var SplFileInfo $file */
        foreach ($this->getFinder()->files()->ignoreDotFiles(false)->in(__DIR__ . '/../../templates/cs')->name('.eslintrc') as $file) {
            $configPath = $root . '/' . $file->getFilename();
            $this->getFileSystem()->copy(
                $file->getRealPath(),
                $configPath
            );
            $this->io->success('Copied lint config from vendor package');
        }
    }

}
