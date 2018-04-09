<?php

namespace Configuration\Service\Factory;

use Configuration\Service\EnvironmentManager;
use Interop\Container\ContainerInterface;
use User\Service\RbacManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class EnvironmentManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $rbacManager = $container->get(RbacManager::class);

        return new EnvironmentManager($entityManager, $rbacManager);
    }
}
