<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create();

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'concat_space' => ['spacing' => 'one'],
        'php_unit_method_casing' => ['case' => 'camel_case'],
        'phpdoc_to_comment' => false,
        'phpdoc_separation' => false,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters', 'match']],
        'no_unused_imports' => true,
        'heredoc_indentation' => true,
        'ternary_to_null_coalescing' => true,
        'assign_null_coalescing_to_coalesce_equal' => true,
        'single_line_throw' => false,
    ])
    ->setFinder($finder)
;
