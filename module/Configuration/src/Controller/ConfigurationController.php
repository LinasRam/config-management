<?php

namespace Configuration\Controller;

use Configuration\Form\ConfigurationForm;
use Configuration\Form\ConfigurationGroupForm;
use Configuration\Service\ConfigurationGroupManager;
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

        $configurationGroup = $this->configurationGroupManager->getConfigurationGroup($id);

        if ($configurationGroup == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $title = $configurationGroup->getApplication()->getName()
            . '/' . $configurationGroup->getEnvironment()->getName();

        $parentGroups = array_reverse(
            $this->configurationManager->getParentGroupsRecursively($configurationGroup),
            true
        );

        $configurationForm = new ConfigurationForm();
        $configurationForm->setData(['config_group' => $id]);

        $configurationGroupForm = new ConfigurationGroupForm();
        $configurationGroupForm->setData(
            ['parent_config_group' => reset($parentGroups)]
        );

        return new ViewModel([
            'title' => $title,
            'parentGroups' => $parentGroups,
            'configurationGroup' => $configurationGroup,
            'configurationForm' => $configurationForm,
            'configurationGroupForm' => $configurationGroupForm,
        ]);
    }

    public function addAction()
    {
        $form = new ConfigurationForm();

        $data = $this->params()->fromPost();

        $form->setData($data);

        if ($form->isValid()) {
            $data = $form->getData();

            $this->configurationManager->saveConfiguration($data);

            $this->flashMessenger()->addSuccessMessage('Added new configuration value.');

            return $this->redirect()->toRoute('configurations', ['action' => 'list', 'id' => $data['config_group']]);
        }
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $configuration = $this->configurationManager->getConfiguration($id);

        if ($configuration == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = new ConfigurationForm();

        $data = $this->params()->fromPost();

        $form->setData($data);

        if ($form->isValid()) {
            $data = $form->getData();

            $this->configurationManager->saveConfiguration($data, $configuration);

            $this->flashMessenger()->addSuccessMessage('Updated the configuration.');

            return $this->redirect()->toRoute('configurations', ['action' => 'list', 'id' => $data['config_group']]);
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $configuration = $this->configurationManager->getConfiguration($id);

        if ($configuration == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $groupId = $configuration->getConfigurationGroup()->getId();

        $this->configurationManager->deleteConfiguration($configuration);

        $this->flashMessenger()->addSuccessMessage('Configuration value deleted.');

        return $this->redirect()->toRoute('configurations', ['action' => 'list', 'id' => $groupId]);
    }
}
