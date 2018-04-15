<?php

namespace Configuration\Service\Factory;

use Configuration\Service\ApplicationManager;
use Configuration\Service\ConfigurationGroupManager;
use Interop\Container\ContainerInterface;
use User\Service\RbacManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class ApplicationManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $rbacManager = $container->get(RbacManager::class);
        $configGroupManager = $container->get(ConfigurationGroupManager::class);

        return new ApplicationManager($entityManager, $rbacManager, $configGroupManager);
    }
}
