<?php

declare(strict_types=1);

namespace Clearfacts\Bundle\CodestyleBundle\Tests\Command;

use Clearfacts\Bundle\CodestyleBundle\Command\CopyCsConfigCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CopyCsConfigCommandTest extends TestCase
{
    private const PHPCS_PATH = __DIR__ . '/.php-cs-fixer.dist.php';
    private const ESLINT_PATH = __DIR__ . '/.eslintrc.dist';

    public function setUp(): void
    {
        @unlink(self::PHPCS_PATH);
        @unlink(self::ESLINT_PATH);
    }

    public function tearDown(): void
    {
        @unlink(self::PHPCS_PATH);
        @unlink(self::ESLINT_PATH);
    }

    public function testExecute(): void
    {
        // Given
        $application = new Application();
        $application->add(new CopyCsConfigCommand());

        $command = $application->find('clearfacts:codestyle:copy-cs-config');
        $commandTester = new CommandTester($command);
        
        // When
        $commandTester->execute([
            '--root' => __DIR__,
        ]);

        // Then
        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Preparing to copy cs config', $output);
        $this->assertStringContainsString('[OK] Copied cs config', $output);
        $this->assertTrue(file_exists(self::PHPCS_PATH));
        $this->assertTrue(file_exists(self::ESLINT_PATH));
        $this->assertStringContainsString('PhpCsFixer\Config', file_get_contents(self::PHPCS_PATH));
        $this->assertStringContainsString('eslint:recommended', file_get_contents(self::ESLINT_PATH));
    }
}
