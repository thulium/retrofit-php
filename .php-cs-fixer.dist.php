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
        '@PSR12' => true,

        'curly_braces_position' => [
            'anonymous_classes_opening_brace' => 'next_line_unless_newline_at_signature_end',
        ],
        'declare_strict_types' => true,
        'function_declaration' => [
            'closure_fn_spacing' => 'none',
        ],
        'trailing_comma_in_multiline' => [
            'elements' => ['arguments', 'arrays', 'match', 'parameters'],
        ],
    ]);
