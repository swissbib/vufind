<?php

return [
    'router' => [
        'routes' => [
            'libraries-index' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/Libraries',
                    'defaults' => [
                        'controller' => 'libraries',
                        'action'     => 'index'
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'invokables' => [
            'libraries' => 'Libadmin\Controller\LibrariesController'
        ]
    ]
];
