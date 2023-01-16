<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'strict_comparison' => true,
        'no_superfluous_phpdoc_tags' => false,
        'no_empty_phpdoc' => false,
    ])
    ->setRiskyAllowed(true);
