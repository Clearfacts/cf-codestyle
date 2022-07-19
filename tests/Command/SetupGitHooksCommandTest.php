<?php

declare(strict_types=1);

namespace Clearfacts\Bundle\CodestyleBundle\Tests\Command;

use Clearfacts\Bundle\CodestyleBundle\Command\SetupGitHooksCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SetupGitHooksCommandTest extends TestCase
{
    private const GIT_HOOKS_PATH = __DIR__ . '/.git/hooks/';

    public function setUp(): void
    {
        $this->removeAllFiles(self::GIT_HOOKS_PATH);
    }

    public function tearDown(): void
    {
        $this->removeAllFiles(self::GIT_HOOKS_PATH);
    }

    public function testExecute(): void
    {
        // Given
        $application = new Application();
        $application->add(new SetupGitHooksCommand());

        $command = $application->find('clearfacts:codestyle:hooks-setup');
        $commandTester = new CommandTester($command);
        
        // When
        $commandTester->execute([
            '--root' => __DIR__,
            '--custom-hooks-dir' => '/Resources/',
        ]);

        // Then
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[OK] Custom hooks copied', $output);
        $this->assertStringContainsString('[OK] Default hooks copied', $output);
        $this->assertFileExists(self::GIT_HOOKS_PATH . 'pre-commit');
        $this->assertFileExists(self::GIT_HOOKS_PATH . 'pre-commit-phpcs');
        $this->assertFileExists(self::GIT_HOOKS_PATH . 'pre-commit-eslint');
        $this->assertFileExists(self::GIT_HOOKS_PATH . 'pre-commit-twig');
        $this->assertFileExists(self::GIT_HOOKS_PATH . 'test-hook');
        $this->assertStringContainsString('test-hook', file_get_contents(self::GIT_HOOKS_PATH . '/pre-commit'));
    }

    private function removeAllFiles(string $dir): void
    {
        $files = @scandir($dir) ?: [];
        foreach ($files as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }

            unlink($dir . $file);
        }
        @rmdir(__DIR__ . '/.git/hooks');
        @rmdir(__DIR__ . '/.git');
    }
}
