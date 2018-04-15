<?php

namespace Configuration\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="application")
 */
class Application
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
     * @var string
     *
     * @ORM\Column(name="description")
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="date_created")
     */
    protected $dateCreated;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Configuration\Entity\Environment")
     * @ORM\JoinTable(name="application_environment",
     *      joinColumns={@ORM\JoinColumn(name="application_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="environment_id", referencedColumnName="id")}
     *      )
     */
    protected $environments;

    /**
     * @ORM\OneToMany(targetEntity="Configuration\Entity\ConfigurationGroup", mappedBy="application", orphanRemoval=true)
     */
    protected $configurationGroups;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->environments = new ArrayCollection();
        $this->configurationGroups = new ArrayCollection();
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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDateCreated(): string
    {
        return $this->dateCreated;
    }

    /**
     * @param string $dateCreated
     */
    public function setDateCreated(string $dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return ArrayCollection
     */
    public function getEnvironments()
    {
        return $this->environments;
    }

    /**
     * @param Environment $environment
     */
    public function addEnvironment(Environment $environment)
    {
        $this->environments->add($environment);
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getConfigurationGroups()
    {
        return $this->configurationGroups;
    }
}
