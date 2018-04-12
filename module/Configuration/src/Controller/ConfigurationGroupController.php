<?php

namespace Configuration\Controller;

use Configuration\Form\ConfigurationGroupForm;
use Configuration\Service\ConfigurationGroupManager;
use Zend\Mvc\Controller\AbstractActionController;

class ConfigurationGroupController extends AbstractActionController
{
    /**
     * @var ConfigurationGroupManager
     */
    private $configGroupManager;

    /**
     * ConfigurationGroupController constructor.
     * @param ConfigurationGroupManager $configGroupManager
     */
    public function __construct(ConfigurationGroupManager $configGroupManager)
    {
        $this->configGroupManager = $configGroupManager;
    }

    public function addAction()
    {
        $form = new ConfigurationGroupForm();

        $data = $this->params()->fromPost();

        $form->setData($data);

        if ($form->isValid()) {
            $data = $form->getData();

            $this->configGroupManager->saveConfigurationGroup($data);

            $this->flashMessenger()->addSuccessMessage('Added new configuration value.');

            return $this->redirect()->toRoute('configurations', ['action' => 'list', 'id' => $data['parent_config_group']]);
        }
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $configurationGroup = $this->configGroupManager->getConfigurationGroup($id);

        if ($configurationGroup == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = new ConfigurationGroupForm();

        $data = $this->params()->fromPost();

        $form->setData($data);

        if ($form->isValid()) {
            $data = $form->getData();

            $this->configGroupManager->saveConfigurationGroup($data, $configurationGroup);

            $this->flashMessenger()->addSuccessMessage('Updated the configuration.');

            return $this->redirect()->toRoute(
                'configurations',
                ['action' => 'list', 'id' => $data['parent_config_group']]
            );
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $configurationGroup = $this->configGroupManager->getConfigurationGroup($id);

        if ($configurationGroup == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $parentGroupId = $configurationGroup->getParentGroups()->first()->getId();

        $this->configGroupManager->deleteConfigurationGroup($configurationGroup);

        $this->flashMessenger()->addSuccessMessage('Configuration value deleted.');

        return $this->redirect()->toRoute('configurations', ['action' => 'list', 'id' => $parentGroupId]);
    }
}
