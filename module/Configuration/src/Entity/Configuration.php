<?php

namespace Configuration\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use User\Entity\Role;

/**
 * @ORM\Entity
 * @ORM\Table(name="configuration")
 */
class Configuration
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="config_key")
     */
    protected $key;

    /**
     * @var string
     *
     * @ORM\Column(name="config_value")
     */
    protected $value;

    /**
     * @var ConfigurationGroup
     *
     * @ORM\ManyToOne(targetEntity="ConfigurationGroup", inversedBy="configurations")
     * @ORM\JoinColumn(name="config_group_id", referencedColumnName="id")
     */
    protected $configurationGroup;

    /**
     * @ORM\ManyToMany(targetEntity="User\Entity\Role")
     * @ORM\JoinTable(name="configuration_role",
     *      joinColumns={@ORM\JoinColumn(name="configuration_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    private $restrictedToRoles;

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
        $this->restrictedToRoles = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return ConfigurationGroup
     */
    public function getConfigurationGroup(): ConfigurationGroup
    {
        return $this->configurationGroup;
    }

    /**
     * @param ConfigurationGroup $configurationGroup
     */
    public function setConfigurationGroup(ConfigurationGroup $configurationGroup)
    {
        $this->configurationGroup = $configurationGroup;
    }

    /**
     * @return mixed
     */
    public function getRestrictedToRoles()
    {
        return $this->restrictedToRoles;
    }

    /**
     * @return array
     */
    public function getRestrictedToRolesIds()
    {
        $ids = [];

        /** @var Role $role */
        foreach ($this->restrictedToRoles as $role) {
            $ids[] = $role->getId();
        }

        return $ids;
    }

    /**
     * @param Role $role
     */
    public function addRestrictedToRole(Role $role)
    {
        $this->restrictedToRoles->add($role);
    }
}
