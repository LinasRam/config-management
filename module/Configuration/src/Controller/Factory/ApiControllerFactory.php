<?php

namespace Configuration\Controller\Factory;

use Configuration\Controller\ApiController;
use Configuration\Service\ConfigurationGroupManager;
use Configuration\Service\ConfigurationManager;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class ApiControllerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return ApiController
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $configurationManager = $container->get(ConfigurationManager::class);
        $configurationGroupManager = $container->get(ConfigurationGroupManager::class);

        return new ApiController($configurationManager, $configurationGroupManager);
    }
}
