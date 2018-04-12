<?php

namespace Configuration\Entity;

use Doctrine\ORM\Mapping as ORM;

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
}
