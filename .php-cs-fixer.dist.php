<?php

declare(strict_types=1);

include 'vendor/autoload.php';

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP80Migration:risky' => true,
        '@PHP82Migration' => true,
        '@PSR12:risky' => true,

        'binary_operator_spaces' => true,
        'cast_spaces' => [
            'space' => 'none',
        ],
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'one',
                'method' => 'one',
                'property' => 'one',
                'trait_import' => 'none',
                'case' => 'none',
            ],
        ],
        'class_reference_name_casing' => true,
        'comment_to_phpdoc' => true,
        'explicit_indirect_variable' => true,
        'fopen_flag_order' => true,
        'fully_qualified_strict_types' => true,
        'function_declaration' => [
            'closure_fn_spacing' => 'none',
        ],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'lambda_not_used_import' => true,
        'logical_operators' => true,
        'multiline_comment_opening_closing' => true,
        'native_function_casing' => true,
        'native_function_type_declaration_casing' => true,
        'no_empty_comment' => true,
        'no_leading_namespace_whitespace' => true,
        'no_short_bool_cast' => true,
        'no_unused_imports' => true,
        'no_unneeded_import_alias' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unneeded_curly_braces' => true,
        'no_useless_concat_operator' => true,
        'no_useless_else' => true,
        'no_useless_nullsafe_operator' => true,
        'nullable_type_declaration' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'object_operator_without_whitespace' => true,
        'operator_linebreak' => [
            'only_booleans' => true,
            'position' => 'end',
        ],
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'case',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public_static',
                'property_protected_static',
                'property_private_static',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'method_public',
                'method_public_static',
                'magic',
                'destruct',
                'method_protected',
                'method_private',
                'method_protected_static',
                'method_private_static',
            ],
        ],
        'return_type_declaration' => true,
        'single_class_element_per_statement' => [
            'elements' => [
                'const',
                'property',
            ],
        ],
        'single_line_comment_spacing' => true,
        'trailing_comma_in_multiline' => [
            'elements' => [
                'arguments',
                'arrays',
                'match',
                'parameters',
            ],
        ],
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => [
            'ensure_single_space' => true,
        ],
    ]);
