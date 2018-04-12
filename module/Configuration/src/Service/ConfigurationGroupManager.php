<?php

namespace Configuration\Service;

use Configuration\Entity\ConfigurationGroup;
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
