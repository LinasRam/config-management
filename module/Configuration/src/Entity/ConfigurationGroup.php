<?php

namespace Configuration\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="config_group")
 */
class ConfigurationGroup
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
     * @ORM\Column(name="name")
     */
    protected $name;

    /**
     * @var Application
     *
     * @ORM\ManyToOne(targetEntity="Application")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     */
    protected $application;

    /**
     * @var Environment
     *
     * @ORM\ManyToOne(targetEntity="Environment")
     * @ORM\JoinColumn(name="environment_id", referencedColumnName="id")
     */
    protected $environment;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ConfigurationGroup", inversedBy="childGroups")
     * @ORM\JoinTable(name="config_group_hierarchy",
     *      joinColumns={@ORM\JoinColumn(name="child_config_group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="parent_config_group_id", referencedColumnName="id")}
     *      )
     */
    protected $parentGroups;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ConfigurationGroup", mappedBy="parentGroups")
     */
    protected $childGroups;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Configuration", mappedBy="configurationGroup", orphanRemoval=true)
     */
    protected $configurations;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_root", type="boolean")
     */
    protected $isRoot = false;

    /**
     * ConfigurationGroup constructor.
     */
    public function __construct()
    {
        $this->parentGroups = new ArrayCollection();
        $this->childGroups = new ArrayCollection();
        $this->configurations = new ArrayCollection();
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @param Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return Environment
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    /**
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getParentGroups()
    {
        return $this->parentGroups;
    }

    /**
     * @param ConfigurationGroup $configurationGroup
     */
    public function addParentGroup(ConfigurationGroup $configurationGroup)
    {
        $this->parentGroups->add($configurationGroup);
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getChildGroups()
    {
        return $this->childGroups;
    }

    /**
     * @param ConfigurationGroup $configurationGroup
     */
    public function addChildGroup(ConfigurationGroup $configurationGroup)
    {
        $this->childGroups->add($configurationGroup);
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getConfigurations()
    {
        return $this->configurations;
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->isRoot;
    }

    /**
     * @param bool $isRoot
     */
    public function setIsRoot(bool $isRoot)
    {
        $this->isRoot = $isRoot;
    }
}
