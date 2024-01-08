<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('docker');

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@Symfony' => true,
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
    'ordered_imports' => true,
    'not_operator_with_successor_space' => true,
    'trailing_comma_in_multiline' => true,
    'phpdoc_align' => true,
    'fully_qualified_strict_types' => false,
    'global_namespace_import' => [
        'import_classes' => true,
        'import_constants' => true,
        'import_functions' => true,
    ],
])
    ->setFinder($finder);
