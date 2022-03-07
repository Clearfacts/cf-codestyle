<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create();

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'concat_space' => ['spacing' => 'one'],
        'php_unit_method_casing' => ['case' => 'camel_case'],
        'phpdoc_to_comment' => false,
    ])
    ->setFinder($finder)
;
