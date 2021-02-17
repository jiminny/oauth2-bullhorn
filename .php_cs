<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src/')
    ->exclude('tests')
;

return (new PhpCsFixer\Config(''))
    ->setHideProgress(false)
    ->setRules([
        '@Symfony'                    => true,
        'single_import_per_statement' => false,
        'concat_space'                => ['spacing' => 'one'],
        'array_syntax'                => ['syntax' => 'short'],
        'yoda_style'                  => false,
        'phpdoc_to_comment'           => false,
        'binary_operator_spaces'      => [
            'operators' => [
                '='  => 'align_single_space_minimal',
                '=>' => 'align_single_space',
            ],
        ],
    ])
    ->setFinder($finder)
;
