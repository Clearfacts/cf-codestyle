<?php

declare(strict_types=1);

namespace Clearfacts\Bundle\CodestyleBundle\Tests\Command;

use Clearfacts\Bundle\CodestyleBundle\Command\SetupGitHooksCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SetupGitHooksCommandTest extends TestCase
{
    public function testExecute()
    {
        // Given
        $gitHooksPath = __DIR__ . '/.git/hooks/';
        $this->removeAllFiles($gitHooksPath);
        
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
        $this->assertStringContainsString('Preparing git-hooks', $output);
        $this->assertStringContainsString('Git hooks copied!', $output);
        $this->assertTrue(file_exists($gitHooksPath . 'pre-commit'));
        $this->assertTrue(file_exists($gitHooksPath . 'pre-commit-phpcs'));
        $this->assertTrue(file_exists($gitHooksPath . 'test-hook'));
        $this->assertStringContainsString('test-hook', file_get_contents($gitHooksPath . '/pre-commit'));

        $this->removeAllFiles($gitHooksPath);
    }

    private function removeAllFiles(string $dir)
    {
        foreach (scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }

            unlink($dir . $file);
        }
        @rmdir(__DIR__ . '/.git/hooks');
        @rmdir(__DIR__ . '/.git');
    }
}