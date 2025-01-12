<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(['build', 'vendor'])
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'align_multiline_comment' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'combine_consecutive_issets' => true,
        'date_time_immutable' => null,
        'declare_strict_types' => true,
        'function_to_constant' => true,
        'header_comment' => ['header' => ''],
        'mb_str_functions' => true,
        'method_chaining_indentation' => true,
        'multiline_whitespace_before_semicolons' => true,
        'native_constant_invocation' => true,
        'native_function_invocation' => true,
        'no_alternative_syntax' => true,
        'no_homoglyph_names' => true,
        'no_null_property_initialization' => true,
        'no_superfluous_elseif' => true,
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
        ],
        'no_useless_else' => true,
//        'nullable_type_declaration_for_default_null_value' => [
//            'use_nullable_type_declaration' => false,
//        ],
        'ordered_imports' => true,
        'ordered_interfaces' => true,
        'phpdoc_line_span' => [
            'const' => 'single',
            'property' => 'single',
        ],
        'phpdoc_no_empty_return' => true,
        'php_unit_expectation' => true,
        'php_unit_internal_class' => true,
        'php_unit_method_casing' => null,
        'php_unit_mock' => true,
        'php_unit_mock_short_will_return' => true,
        'php_unit_namespaced' => true,
        'php_unit_no_expectation_annotation' => true,
        'php_unit_set_up_tear_down_visibility' => true,
        'php_unit_test_case_static_method_calls' => [
            'call_type' => 'this',
        ],
        'phpdoc_align' => false,
        'phpdoc_order' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'simple_to_complex_string_variable' => true,
        'single_line_throw' => false,
        'static_lambda' => true,
        'void_return' => false, // PHP 7.0 compatibility
        'yoda_style' => false,
    ])
    ->setFinder($finder);
