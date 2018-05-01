<?php

namespace Configuration;

use Configuration\Controller\Factory\ApiControllerFactory;
use Configuration\Controller\Factory\ApplicationControllerFactory;
use Configuration\Controller\Factory\ConfigurationControllerFactory;
use Configuration\Controller\Factory\ConfigurationGroupControllerFactory;
use Configuration\Controller\Factory\EnvironmentControllerFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\ApiController::class => ApiControllerFactory::class,
            Controller\ApplicationController::class => ApplicationControllerFactory::class,
            Controller\ConfigurationController::class => ConfigurationControllerFactory::class,
            Controller\ConfigurationGroupController::class => ConfigurationGroupControllerFactory::class,
            Controller\EnvironmentController::class => EnvironmentControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'api' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api/v1[/:action[/:application][/:environment]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'application' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'environment' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiController::class,
                    ],
                ],
            ],
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
            'environments' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/environments[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => Controller\EnvironmentController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'configurations' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/configurations[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => Controller\ConfigurationController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'configuration-groups' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/configuration-groups[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => Controller\ConfigurationGroupController::class,
                    ],
                ],
            ],
        ],
    ],
    'access_filter' => [
        'controllers' => [
            Controller\ApiController::class => [
                ['actions' => ['configurations'], 'allow' => '*'],
            ],
            Controller\ApplicationController::class => [
                ['actions' => ['index', 'view', 'add', 'edit', 'delete'], 'allow' => '+application.manage'],
            ],
            Controller\EnvironmentController::class => [
                ['actions' => ['index', 'view', 'add', 'edit', 'delete'], 'allow' => '+environment.manage'],
            ],
            Controller\ConfigurationController::class => [
                ['actions' => ['index', 'list', 'add', 'edit', 'delete'], 'allow' => '@'],
            ],
            Controller\ConfigurationGroupController::class => [
                ['actions' => ['add', 'edit', 'delete'], 'allow' => '@'],
            ],
        ]
    ],
    'service_manager' => [
        'factories' => [
            Service\ApplicationManager::class => Service\Factory\ApplicationManagerFactory::class,
            Service\EnvironmentManager::class => Service\Factory\EnvironmentManagerFactory::class,
            Service\ConfigurationManager::class => Service\Factory\ConfigurationManagerFactory::class,
            Service\ConfigurationGroupManager::class => Service\Factory\ConfigurationGroupManagerFactory::class,
            Service\FileGenerator::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Configuration' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\ConfigBreadcrumbs::class => InvokableFactory::class,
        ],
        'aliases' => [
            'configBreadcrumbs' => View\Helper\ConfigBreadcrumbs::class,
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
