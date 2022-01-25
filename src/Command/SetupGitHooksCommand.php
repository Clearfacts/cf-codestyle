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

    /**
     * @var SymfonyStyle
     */
    private $io;

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Setup git hooks for code quality')
            ->addOption('root', 'r', InputOption::VALUE_OPTIONAL, 'Root directory of the project', '.')
            ->addOption('custom-hooks-dir', 'chr', InputOption::VALUE_OPTIONAL, 'Extra hooks to be checked pre-commit', null);
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('Preparing git-hooks');

        $this->setup($input->getOption('root'), $input->getOption('custom-hooks-dir'));

        return 0;
    }

    private function setup(string $root, ?string $customHooksDir): void
    {
        $customHooks = [];
        /** @var SplFileInfo $file */
        if (null !== $customHooksDir) {
            foreach ($this->getFinder()->files()->in($root . $customHooksDir) as $file) {
                $gitHooksPath = $root . '/.git/hooks/' . $file->getFilename();
                $this->getFileSystem()->copy(
                    $file->getRealPath(),
                    $gitHooksPath,
                    true
                );

                $customHooks[] = $file->getFilename();

                $this->getFileSystem()->chmod([$gitHooksPath], 0755);
            }

            $this->io->success('Custom hooks copied');
        }

        foreach ($this->getFinder()->files()->in(__DIR__ . '/../../templates/hooks') as $file) {
            $gitHooksPath = $root . '/.git/hooks/' . $file->getFilename();
            $this->getFileSystem()->remove($gitHooksPath);
            $this->getFileSystem()->dumpFile(
                $gitHooksPath,
                strtr($file->getContents(), [
                    '%custom_hooks%' => implode(' ', $customHooks),
                ])
            );

            $this->getFileSystem()->chmod([$gitHooksPath], 0755);
        }

        $this->io->success('Default hooks copied');
    }
}
