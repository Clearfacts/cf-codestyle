<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create();

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'concat_space' => ['spacing' => 'one'],
        'declare_strict_types' => true,
        'declare_equal_normalize' => true,
        'declare_parentheses' => true,
    ])
    ->setFinder($finder)
;
