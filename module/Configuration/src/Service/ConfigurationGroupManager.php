<?php

namespace Configuration\Service;

use Configuration\Entity\Application;
use Configuration\Entity\ConfigurationGroup;
use Configuration\Entity\Environment;
use Doctrine\ORM\EntityManager;
use Exception;
use User\Service\RbacManager;

class ConfigurationGroupManager
{
    /**
     * Doctrine entity manager.
     * @var EntityManager
     */
    private $entityManager;

    /**
     * RBAC manager.
     * @var RbacManager
     */
    private $rbacManager;

    /**
     * ConfigurationManager constructor.
     * @param EntityManager $entityManager
     * @param RbacManager $rbacManager
     */
    public function __construct(EntityManager $entityManager, RbacManager $rbacManager)
    {
        $this->entityManager = $entityManager;
        $this->rbacManager = $rbacManager;
    }

    /**
     * @param int $id
     * @return ConfigurationGroup|null
     */
    public function getConfigurationGroup(int $id): ?ConfigurationGroup
    {
        /** @var ConfigurationGroup $configurationGroup */
        $configurationGroup = $this->entityManager->getRepository(ConfigurationGroup::class)->find($id);

        return $configurationGroup;
    }

    /**
     * @return array
     */
    public function getAccessibleRootGroups(): array
    {
        $rootGroups = $this->entityManager->getRepository(ConfigurationGroup::class)->findBy(
            ['isRoot' => true],
            ['name' => 'ASC']
        );

        $accessibleRootGroups = [];
        /** @var ConfigurationGroup $rootGroup */
        foreach ($rootGroups as $rootGroup) {
            $permission = 'manage.' . $rootGroup->getApplication()->getName() . '.'
                . $rootGroup->getEnvironment()->getName();
            if ($this->rbacManager->isGranted(null, $permission)) {
                $accessibleRootGroups[] = $rootGroup;
            }
        }

        return $accessibleRootGroups;
    }

    /**
     * @param string $application
     * @param string $environment
     * @return ConfigurationGroup|null
     */
    public function getRootConfigurationGroup(string $application, string $environment): ?ConfigurationGroup
    {
        $application = $this->entityManager->getRepository(Application::class)->findByName($application);
        $environment = $this->entityManager->getRepository(Environment::class)->findByName($environment);

        /** @var ConfigurationGroup $configurationGroup */
        $configurationGroup = $this->entityManager->getRepository(ConfigurationGroup::class)
            ->findOneBy(['application' => $application, 'environment' => $environment, 'isRoot' => true]);

        return $configurationGroup;
    }

    /**
     * @param array $data
     * @param ConfigurationGroup|null $configurationGroup
     * @throws Exception
     */
    public function saveConfigurationGroup(array $data, ConfigurationGroup $configurationGroup = null)
    {
        /** @var ConfigurationGroup $existingConfiguration */
        $existingConfiguration = $this->entityManager->getRepository(ConfigurationGroup::class)
            ->findOneByName($data['name']);

        if (!is_null($existingConfiguration) && $existingConfiguration != $configurationGroup) {
            throw new Exception('Configuration group with such name already exists');
        }

        if (!$configurationGroup) {
            /** @var ConfigurationGroup $parentGroup */
            $parentGroup = $this->entityManager->getRepository(ConfigurationGroup::class)
                ->find($data['parent_config_group']);

            $configurationGroup = new ConfigurationGroup();
            $configurationGroup->setApplication($parentGroup->getApplication());
            $configurationGroup->setEnvironment($parentGroup->getEnvironment());
            $configurationGroup->addParentGroup($parentGroup);
        }

        $configurationGroup->setName($data['name']);

        $this->entityManager->persist($configurationGroup);

        $this->entityManager->flush();
    }

    /**
     * @param ConfigurationGroup $configurationGroup
     */
    public function deleteConfigurationGroup(ConfigurationGroup $configurationGroup)
    {
        $configurationGroup->getConfigurations()->clear();
        $this->entityManager->remove($configurationGroup);
        $this->entityManager->flush();
    }
}
