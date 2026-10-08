<?php

$EM_CONF[$_EXTKEY] = [
    'title'       => 'Connetation TYPO3 Web Toolkit: Elements',
    'description' => 'Content elements for TYPO3 LTS 14',
    'category'    => 'plugin',
    'author'      => 'Connetation Web Engineering GmbH',
    'author_email' => 'office@connetation.at',
    'state'       => 'stable',
    'version'     => '3.1.0',
    'constraints' => [
        'depends' => [
            'typo3'           => '14.0.0-14.99.99',
            'container'       => '3.2.0-3.99.99',
            'conn_t3wtk_base' => '3.0.0-3.99.99',
        ],
    ],
];
