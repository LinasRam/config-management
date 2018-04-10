<?php

namespace Configuration\Service\Factory;

use Configuration\Service\ConfigurationManager;
use Interop\Container\ContainerInterface;
use User\Service\RbacManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class ConfigurationManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $rbacManager = $container->get(RbacManager::class);

        return new ConfigurationManager($entityManager, $rbacManager);
    }
}
