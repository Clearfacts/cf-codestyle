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

    protected static $defaultName = 'clearfacts:codestyle:copy-cs-config';

    private ?SymfonyStyle $io = null;
    private bool $quiet = false;
    private string $root = '.';

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Copy latest code sniffing config')
            ->addOption('root', 'r', InputOption::VALUE_OPTIONAL, 'Root directory of the project', '.')
            ->addOption('quiet', 'q', InputOption::VALUE_NONE, "Don't output anything unless an action was actually undertaken")
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->root = $input->getOption('root');
        $this->quiet = (bool) $input->getOption('quiet');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->quiet) {
            $this->io?->title('Preparing to copy cs config');
        }

        try {
            $this->copyConfig($this->root . '/.php-cs-fixer.dist.php', 'phpcs', '.php-cs-fixer-8.1.dist.php');
            $this->copyConfig($this->root . '/.eslintrc.dist', 'eslint', '.eslintrc.dist');
        } catch (\Throwable $e) {
            $this->io?->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function copyConfig(string $destination, string $type, string $template): void
    {
        $files = $this->getFinder()
            ->ignoreDotFiles(false)
            ->in(__DIR__ . '/../../templates/cs')
            ->files()
            ->name($template);

        if ($files->count() > 1) {
            throw new \RuntimeException("Expected to find a single $type config file but found multiple");
        }

        foreach ($files as $file) {
            $this->getFileSystem()->copy(
                $file->getRealPath(),
                $destination,
                true,
            );
        }

        $this->io?->success("Copied $type config from vendor package");
    }

}
