<?php

namespace Configuration\Controller;

use Configuration\Service\ConfigurationGroupManager;
use Configuration\Service\ConfigurationManager;
use Configuration\Service\FileGenerator;
use User\Service\UserManager;
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
     * @var UserManager
     */
    private $userManager;

    /**
     * @var FileGenerator
     */
    private $fileGenerator;

    /**
     * ConfigurationController constructor.
     * @param ConfigurationManager $configurationManager
     * @param ConfigurationGroupManager $configurationGroupManager
     * @param UserManager $userManager
     * @param FileGenerator $fileGenerator
     */
    public function __construct(
        ConfigurationManager $configurationManager,
        ConfigurationGroupManager $configurationGroupManager,
        UserManager $userManager,
        FileGenerator $fileGenerator
    ) {
        $this->configurationManager = $configurationManager;
        $this->configurationGroupManager = $configurationGroupManager;
        $this->userManager = $userManager;
        $this->fileGenerator = $fileGenerator;
    }

    public function configurationsAction()
    {
        $application = $this->params()->fromRoute('application');
        $environment = $this->params()->fromRoute('environment');
        $token = $this->params()->fromQuery('token', '');
        $format = $this->params()->fromQuery('format', 'json');

        $user = $this->userManager->getUserByToken($token);

        if (!$user) {
            $this->getResponse()->setStatusCode(403);
            return new JsonModel(['message' => 'Configuration not accessible']);
        }

        if (!$application || !$environment) {
            $this->getResponse()->setStatusCode(404);
            return new JsonModel([]);
        }

        $rootConfigurationGroup = $this->configurationGroupManager->getRootConfigurationGroup(
            $application,
            $environment
        );

        if (!$rootConfigurationGroup) {
            $this->getResponse()->setStatusCode(404);
            return new JsonModel([]);
        }

        try {
            $configurations = $this->configurationManager->getConfigurationsByRootGroupRecursively(
                $rootConfigurationGroup,
                $user
            );
        } catch (\Exception $e) {
            $this->getResponse()->setStatusCode(403);
            return new JsonModel(['message' => $e->getMessage()]);
        }

        $file = $this->fileGenerator->getFileContentFromArray($configurations, $format);

        $response = $this->getResponse();

        $response->setContent($file);
        $response
            ->getHeaders()
            ->addHeaderLine('Content-Type', $format);

        return $response;
    }
}
