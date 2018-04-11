<?php

namespace Configuration\Controller;

use Configuration\Form\ConfigurationForm;
use Configuration\Service\ConfigurationManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ConfigurationController extends AbstractActionController
{
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * ConfigurationController constructor.
     * @param ConfigurationManager $configurationManager
     */
    public function __construct(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    public function indexAction()
    {
        $configurationGroups = $this->configurationManager->getRootGroups();

        return new ViewModel(['configurationGroups' => $configurationGroups]);
    }

    public function listAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $configurationGroup = $this->configurationManager->getConfigurationGroup($id);

        if ($configurationGroup == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $title = $configurationGroup->getApplication()->getName()
            . '/' . $configurationGroup->getEnvironment()->getName();

        return new ViewModel([
            'title' => $title,
            'parentGroups' => array_reverse(
                $this->configurationManager->getParentGroupsRecursively($configurationGroup),
                true
            ),
            'configurationGroup' => $configurationGroup,
            'configurationForm' => new ConfigurationForm(),
        ]);
    }
}
