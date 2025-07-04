<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'ViewHelpers Collection',
    'description' => 'A collection of more or less useful ViewHelpers',
    'state' => 'stable',
    'category' => 'misc',
    'author' => 'Thomas Rawiel',
    'author_email' => 'thomas.rawiel@gmail.com',
    'version' => '1.15.5',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
            'extbase' => '12.4.0-13.4.99',
            'fluid_styled_content' => '12.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
