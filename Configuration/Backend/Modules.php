<?php

declare(strict_types=1);

return [
    'conn_elements_wiki' => [
        'parent'   => 'content',
        'position' => ['after' => 'records'],
        'access'   => 'user',
        'path'     => '/module/conn-elements/wiki',
        'labels'   => [
            'title' => 'LLL:EXT:conn_elements/Resources/Private/Language/locallang_be.xlf:module.wiki.title',
        ],
        'routes' => [
            '_default' => [
                'target' => \Connetation\Elements\Controller\WikiModuleController::class . '::indexAction',
            ],
            'pages' => [
                'target' => \Connetation\Elements\Controller\WikiModuleController::class . '::pagesAction',
            ],
            'categories' => [
                'target' => \Connetation\Elements\Controller\WikiModuleController::class . '::categoriesAction',
            ],
        ],
        'iconIdentifier' => 'conn-ce-wiki',
        'templates' => [
            'templateRootPaths' => [
                10 => 'EXT:conn_elements/Resources/Private/Templates/',
            ],
            'partialRootPaths' => [
                10 => 'EXT:conn_elements/Resources/Private/Partials/',
            ],
            'layoutRootPaths' => [
                10 => 'EXT:conn_elements/Resources/Private/Layouts/',
            ],
        ],
    ],
];
