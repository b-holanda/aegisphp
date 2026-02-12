<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/packages',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->exclude([
        'vendor',
        'coverage',
        '.phpunit.cache',
        '.cache',
        'build',
        'dist',
        'tmp',
        '.tmp',
    ]);

return (new Config())
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache')
    ->setFinder($finder)
    ->setRules([
        '@PSR12' => true,
        '@PHP82Migration' => true,
        '@PHPUnit100Migration:risky' => true,

        // Strictness / safety
        'declare_strict_types' => true,
        'strict_param' => true,

        // Consistency
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'single_space'],
        'concat_space' => ['spacing' => 'one'],
        'cast_spaces' => ['space' => 'single'],
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'single_quote' => true,

        // Clean code
        'no_unused_imports' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const'],
        ],
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_order' => true,
        'phpdoc_trim' => true,
        'phpdoc_no_empty_return' => true,

        // Modernization
        'nullable_type_declaration_for_default_null_value' => true,
        'native_function_invocation' => [
            'include' => ['@compiler_optimized'],
            'strict' => false,
        ],

        // Risky but useful
        'modernize_strpos' => true,
        'modernize_types_casting' => true,
    ]);
