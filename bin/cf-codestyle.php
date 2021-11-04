<?php

if (\file_exists(__DIR__.'/../../../autoload.php')) {
    require __DIR__ . '/../../../autoload.php';
} else {
    require __DIR__ . '/../vendor/autoload.php';
}

use Symfony\Component\Console\Application;
use Clearfacts\Bundle\CodestyleBundle\Command\SetupGitHooksCommand;
use Clearfacts\Bundle\CodestyleBundle\Command\CopyCsConfigCommand;

$application = new Application('Clearfacts code quality console', '1.0.0');
$application->add(new SetupGitHooksCommand());
$application->add(new CopyCsConfigCommand());
$application->run();