<?php

namespace Configuration\Controller;

use Configuration\Service\EnvironmentManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EnvironmentController extends AbstractActionController
{
    /**
     * @var EnvironmentManager
     */
    private $environmentManager;

    /**
     * EnvironmentController constructor.
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(EnvironmentManager $environmentManager)
    {
        $this->environmentManager = $environmentManager;
    }

    public function indexAction()
    {
        $environments = $this->environmentManager->getAllEnvironments();

        return new ViewModel(['environments' => $environments]);
    }

    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $environment = $this->environmentManager->getEnvironment($id);

        if ($environment == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel([
            'environment' => $environment
        ]);
    }

    public function addAction()
    {
        $form = $this->environmentManager->getEnvironmentForm();

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $form->setData($data);

            if ($form->isValid()) {
                $data = $form->getData();

                $this->environmentManager->saveEnvironment($data);

                $this->flashMessenger()->addSuccessMessage('Added new environment.');

                return $this->redirect()->toRoute('environments', ['action' => 'index']);
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $environment = $this->environmentManager->getEnvironment($id);

        if ($environment == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $form = $this->environmentManager->getEnvironmentForm($environment);

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $form->setData($data);

            if ($form->isValid()) {
                $data = $form->getData();

                $this->environmentManager->saveEnvironment($data, $environment);

                $this->flashMessenger()->addSuccessMessage('Updated the environment.');

                return $this->redirect()->toRoute('environments', ['action' => 'index']);
            }
        } else {
            $form->setData(array(
                'name' => $environment->getName(),
                'description' => $environment->getDescription()
            ));
        }

        return new ViewModel([
            'form' => $form,
            'environment' => $environment
        ]);
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $environment = $this->environmentManager->getEnvironment($id);

        if ($environment == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $this->environmentManager->deleteEnvironment($environment);

        $this->flashMessenger()->addSuccessMessage('Environment deleted.');

        return $this->redirect()->toRoute('environments', ['action' => 'index']);
    }
}
