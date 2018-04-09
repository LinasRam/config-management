<?php

namespace Configuration;

use Configuration\Controller\Factory\ApplicationControllerFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Zend\Router\Http\Segment;

return [
    'controllers' => [
        'factories' => [
            Controller\ApplicationController::class => ApplicationControllerFactory::class,
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
                ['actions' => ['index', 'view', 'add', 'edit', 'delete'], 'allow' => '+application.manage'],
            ],
        ]
    ],
    'service_manager' => [
        'factories' => [
            Service\ApplicationManager::class => Service\Factory\ApplicationManagerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Configuration' => __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
];
