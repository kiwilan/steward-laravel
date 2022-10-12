<?php

namespace Kiwilan\Steward\Utils;

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

class PhpCsFixerSteward
{
    /**
     * Rules
     * https://mlocati.github.io/php-cs-fixer-configurator.
     */
    public const RULES = [
        '@PhpCsFixer' => true,
        'no_empty_comment' => false,
        'no_extra_blank_lines' => [
            'tokens' => [
                'extra',
                'throw',
                'use',
            ],
        ],
        'not_operator_with_successor_space' => true,
        'php_unit_method_casing' => false,
        'single_line_comment_style' => false,
        'phpdoc_single_line_var_spacing' => true,
        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,
        'lambda_not_used_import' => false,
        'return_assignment' => true,
        'phpdoc_to_comment' => false,
        'ordered_imports' => [
            'imports_order' => [
                'class', 'function', 'const',
            ],
            'sort_algorithm' => 'alpha',
        ],
        'array_indentation' => true,
        'array_syntax' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => false,
        'no_unused_imports' => true,
    ];

    /**
     * Create PHP-CS-Fixer config.
     *
     * @param  array  $rules Optional rules for PHP-CS-Fixer configurator https://mlocati.github.io/php-cs-fixer-configurator.
     */
    public static function make(array $rules = []): \PhpCsFixer\ConfigInterface
    {
        $paths = [];
        $app_path = __DIR__.'/../../../../../app';
        $parent_dir = dirname(__DIR__, 5);
        dump($parent_dir);
        $paths[] = $parent_dir.'/app';
        // $dir = dirname(__DIR__, 2);
        // __DIR__.'/app',
        // __DIR__.'/config',
        // __DIR__.'/database',
        // __DIR__.'/resources',
        // __DIR__.'/tests',

        $finder = Finder::create()
            ->in($paths)
            ->name('*.php')
            ->notName('*.blade.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);

        $config = new Config();

        return $config->setFinder($finder)
          ->setRules(empty($rules) ? PhpCsFixerSteward::RULES : $rules)
          ->setRiskyAllowed(true)
          ->setUsingCache(true);
    }
}
