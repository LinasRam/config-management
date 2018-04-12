<?php

namespace Configuration\Service;

use Configuration\Entity\Configuration;
use Configuration\Entity\ConfigurationGroup;
use Doctrine\ORM\EntityManager;
use Exception;
use User\Service\RbacManager;

class ConfigurationManager
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
     * @return Configuration|null
     */
    public function getConfiguration(int $id): ?Configuration
    {
        /** @var Configuration $configuration */
        $configuration = $this->entityManager->getRepository(Configuration::class)->find($id);

        return $configuration;
    }

    public function getRootGroups(): array
    {
        return $this->entityManager->getRepository(ConfigurationGroup::class)->findBy(
            ['isRoot' => true],
            ['name' => 'ASC']
        );
    }

    public function getParentGroupsRecursively(ConfigurationGroup $configurationGroup, array $parentGroups = []): array
    {
        /** @var ConfigurationGroup $parentGroup */
        if ($parentGroup = $configurationGroup->getParentGroups()->first()) {
            $parentGroups[$parentGroup->getId()] = $parentGroup->getName();
            $parentGroups = $this->getParentGroupsRecursively($parentGroup, $parentGroups);
        }

        return $parentGroups;
    }

    /**
     * @param array $data
     * @param Configuration|null $configuration
     * @throws Exception
     */
    public function saveConfiguration(array $data, Configuration $configuration = null)
    {
        /** @var ConfigurationGroup $configurationGroup */
        $configurationGroup = $this->entityManager
            ->getRepository(ConfigurationGroup::class)->find($data['config_group']);
        /** @var Configuration $existingConfiguration */
        $existingConfiguration = $this->entityManager->getRepository(Configuration::class)
            ->findOneBy(['key' => $data['key'], 'configurationGroup' => $configurationGroup]);

        if (!is_null($existingConfiguration) && $existingConfiguration != $configuration) {
            throw new Exception('Configuration with such key already exists');
        }

        if (!$configuration) {
            $configuration = new Configuration();
        }

        $configuration->setKey($data['key']);
        $configuration->setValue($data['value']);
        $configuration->setConfigurationGroup($configurationGroup);

        $this->entityManager->persist($configuration);

        $this->entityManager->flush();
    }

    /**
     * @param Configuration $configuration
     */
    public function deleteConfiguration(Configuration $configuration)
    {
        $this->entityManager->remove($configuration);
        $this->entityManager->flush();
    }
}
