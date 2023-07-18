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

        'trim_array_spaces' => true,
//        '' => true,
//        'function_declaration' => [
//            'closure_fn_spacing' => 'none',
//        ],
//        'trailing_comma_in_multiline' => [
//            'elements' => ['arguments', 'arrays', 'match', 'parameters'],
//        ],
    ]);
