<?php

declare(strict_types=1);

namespace Clearfacts\Bundle\CodestyleBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class SetupGitHooksCommand extends Command
{
    protected static $defaultName = 'clearfacts:codestyle:hooks-setup';

    protected function configure(): void
    {
        $this
            ->setDescription('Setup git hooks for code quality')
            ->addOption('root', 'r', InputOption::VALUE_OPTIONAL, 'Root directory of the project', '.')
            ->addOption('container', 'c', InputOption::VALUE_OPTIONAL, 'Php container on which the hooks should run', 'php');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Preparing git-hooks');

        $this->setup($input->getOption('root'));

        $io->success('Git hooks copied!');
        return 0;
    }

    private function setup(string $root): void
    {
        /** @var SplFileInfo $file */
        foreach ($this->getFinder()->files()->in(__DIR__ . '/../../templates/hooks') as $file) {
            $gitHooksPath = $root . '/.git/hooks/' . $file->getFilename();
            $this->getFileSystem()->remove($gitHooksPath);
            $this->getFileSystem()->copy(
                $file->getRealPath(),
                $gitHooksPath,
                true
            );

            file_put_contents(
                $gitHooksPath,
                strtr(file_get_contents($gitHooksPath), [
                    '%container%' => $input->getOption('container'),
                ])
            );

            $this->getFileSystem()->chmod([$gitHooksPath], 0755);
        }
    }

    private function getFileSystem(): Filesystem
    {
        return new Filesystem();
    }

    private function getFinder(): Finder
    {
        return new Finder();
    }
}
