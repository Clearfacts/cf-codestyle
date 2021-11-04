<?php

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    throw new \LogicException('Run "composer install" to install the dependencies.');
} 

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Clearfacts\Bundle\CodestyleBundle\Command\SetupGitHooksCommand;
use Clearfacts\Bundle\CodestyleBundle\Command\CopyCsConfigCommand;

$application = new Application('Clearfacts code quality console', '1.0.0');
$application->add(new SetupGitHooksCommand());
$application->add(new CopyCsConfigCommand());
$application->run();
