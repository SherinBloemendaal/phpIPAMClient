<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = new PhpCsFixer\Finder()
    ->in(__DIR__)
    ->exclude('var')
;

return new PhpCsFixer\Config()
    ->setRiskyAllowed(true)
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRules([
        '@PHP81Migration' => true,
        '@PHP80Migration:risky' => true,
        '@PhpCsFixer:risky' => true,
        '@PhpCsFixer' => true,
        'php_unit_test_class_requires_covers' => false,
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;
