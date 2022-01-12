<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create();

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        '@Symfony' => true,
        'blank_line_before_statement' => true,
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_to_comment' => false,
        'declare_strict_types' => true,
    ])
    ->setFinder($finder)
;
