<?php

namespace Configuration;

use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\ApplicationController::class => InvokableFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'applications' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/applications[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => Controller\ApplicationController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'access_filter' => [
        'controllers' => [
            Controller\ApplicationController::class => [
                ['actions' => ['index'], 'allow' => '@'],
            ],
        ]
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Configuration' => __DIR__ . '/../view',
        ],
    ],
];
