<?php
/**
 * Behat Code Coverage
 */
declare(strict_types=1);

$config = new PhpCsFixer\Config();

return $config->setRules(
    [
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP71Migration' => true,
        '@PHP71Migration:risky' => true,
        '@PHP73Migration' => true,
        'concat_space' => ['spacing' => 'one'],
        'fopen_flags' => ['b_mode' => true],
        'native_function_invocation' => ['include' => ['@all']],
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'phpdoc_separation' => false,
        'yoda_style' => false,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => false],
        'header_comment' => [
            'location' => 'after_open',
            'comment_type' => 'PHPDoc',
            'separate' => 'none',
            'header' => 'Behat Code Coverage',
        ],
        'phpdoc_line_span' => true,
    ]
)
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/features')
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests')
            ->append([__FILE__])
    );
