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

        'class_reference_name_casing' => true,
        'native_function_casing' => true,
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => [
            'ensure_single_space' => true,
        ],
//        '' => true,
//        'function_declaration' => [
//            'closure_fn_spacing' => 'none',
//        ],
//        'trailing_comma_in_multiline' => [
//            'elements' => ['arguments', 'arrays', 'match', 'parameters'],
//        ],
    ]);
