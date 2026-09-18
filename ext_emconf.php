<?php

$EM_CONF[$_EXTKEY] = [
    'title'       => 'Conn: Elements',
    'description' => 'Content elements for TYPO3 LTS 14',
    'category'    => 'plugin',
    'author'      => 'Connetation',
    'author_email' => 'i.sapina@connetation.at',
    'state'       => 'stable',
    'version'     => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3'     => '14.0.0-14.99.99',
            'conn_t3kit_base' => '1.0.0-0.0.0',
        ],
    ],
];
