<?php

namespace Configuration\Controller;

use Configuration\Service\ConfigurationGroupManager;
use Configuration\Service\ConfigurationManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class ApiController extends AbstractActionController
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var ConfigurationGroupManager
     */
    private $configurationGroupManager;

    /**
     * ConfigurationController constructor.
     * @param ConfigurationManager $configurationManager
     * @param ConfigurationGroupManager $configurationGroupManager
     */
    public function __construct(
        ConfigurationManager $configurationManager,
        ConfigurationGroupManager $configurationGroupManager
    ) {
        $this->configurationManager = $configurationManager;
        $this->configurationGroupManager = $configurationGroupManager;
    }

    public function configurationsAction()
    {
        $application = $this->params()->fromRoute('application');
        $environment = $this->params()->fromRoute('environment');

        if (!$application || !$environment) {
            $this->getResponse()->setStatusCode(404);
            return new JsonModel();
        }

        $rootConfigurationGroup = $this->configurationGroupManager->getRootConfigurationGroup(
            $application,
            $environment
        );

        if (!$rootConfigurationGroup) {
            $this->getResponse()->setStatusCode(404);
            return new JsonModel();
        }

        $configurations = $this->configurationManager->getConfigurationsByRootGroupRecursively($rootConfigurationGroup);

        return new JsonModel($configurations);
    }
}
