<?php

declare(strict_types=1);

namespace Clearfacts\Bundle\CodestyleBundle\Tests\Command;

use Clearfacts\Bundle\CodestyleBundle\Command\CopyCsConfigCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CopyCsConfigCommandTest extends TestCase
{
    public function testExecute()
    {
        // Given
        $phpcsConfigPath = __DIR__ . '/.php-cs';
        @unlink($phpcsConfigPath);

        $application = new Application();
        $application->add(new CopyCsConfigCommand());

        $command = $application->find('clearfacts:codestyle:copy-cs-config');
        $commandTester = new CommandTester($command);
        
        // When
        $commandTester->execute([
            '--root' => __DIR__,
            '--config-dir' => '.'
        ]);

        // Then
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Preparing to copy cs config', $output);
        $this->assertStringContainsString('Cs config copied!', $output);
        $this->assertTrue(file_exists($phpcsConfigPath));
        $this->assertStringContainsString('PhpCsFixer\Config', file_get_contents($phpcsConfigPath));

        @unlink($phpcsConfigPath);
    }
}