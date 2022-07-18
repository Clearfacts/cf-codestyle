<?php

declare(strict_types=1);

namespace Clearfacts\Bundle\CodestyleBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SetupGitHooksCommand extends Command
{
    use FilesystemTrait;

    protected static $defaultName = 'clearfacts:codestyle:hooks-setup';

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Setup git hooks for code quality')
            ->addOption('root', 'r', InputOption::VALUE_OPTIONAL, 'Root directory of the project', '.')
            ->addOption('custom-hooks-dir', 'chr', InputOption::VALUE_OPTIONAL, 'Extra hooks to be checked pre-commit', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $root = $input->getOption('root');
        $customHooksDir = $input->getOption('custom-hooks-dir');

        $customHooks = [];
        if (null !== $customHooksDir) {
            foreach ($this->getFinder()->files()->in($root . $customHooksDir) as $file) {
                $gitHooksPath = $root . '/.git/hooks/' . $file->getFilename();
                $this->getFileSystem()->copy(
                    $file->getRealPath(),
                    $gitHooksPath,
                    true,
                );

                $customHooks[] = $file->getFilename();

                $this->getFileSystem()->chmod([$gitHooksPath], 0755);
            }
        }

        $io->success('Custom hooks copied');

        foreach ($this->getFinder()->files()->in(__DIR__ . '/../../templates/hooks') as $file) {
            $gitHooksPath = $root . '/.git/hooks/' . $file->getFilename();
            $this->getFileSystem()->remove($gitHooksPath);
            $this->getFileSystem()->dumpFile(
                $gitHooksPath,
                strtr($file->getContents(), [
                    '%custom_hooks%' => implode(' ', $customHooks),
                ]),
            );

            $this->getFileSystem()->chmod([$gitHooksPath], 0755);
        }

        $io->success('Default hooks copied');

        return self::SUCCESS;
    }
}
