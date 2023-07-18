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
        'fopen_flag_order' => true,
        'function_declaration' => [
            'closure_fn_spacing' => 'none',
        ],
        'lambda_not_used_import' => true,
        'multiline_comment_opening_closing' => true,
        'native_function_casing' => true,
        'native_function_type_declaration_casing' => true,
        'no_empty_comment' => true,
        'no_short_bool_cast' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unneeded_curly_braces' => true,
        'no_useless_else' => true,
        'nullable_type_declaration_for_default_null_value' => true,
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
