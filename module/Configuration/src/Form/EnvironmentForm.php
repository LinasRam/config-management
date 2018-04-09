<?php

namespace Configuration\Form;

use Configuration\Entity\Environment;
use Configuration\Validator\EntityByNameExistsValidator;
use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class EnvironmentForm extends Form
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * ApplicationForm constructor.
     * @param EntityManager $entityManager
     * @param Environment|null $environment
     */
    public function __construct(EntityManager $entityManager, Environment $environment = null)
    {
        parent::__construct('environment-form');

        $this->entityManager = $entityManager;
        $this->environment = $environment;

        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements()
    {
        $this->add([
            'type' => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name'
            ],
            'options' => [
                'label' => 'Environment Name',
            ],
        ]);

        $this->add([
            'type' => 'textarea',
            'name' => 'description',
            'attributes' => [
                'id' => 'description'
            ],
            'options' => [
                'label' => 'Description',
            ],
        ]);

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Create',
                'id' => 'submit',
            ],
        ]);

        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);
    }

    private function addInputFilter()
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'name' => 'name',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 128
                    ],
                ],
                [
                    'name' => EntityByNameExistsValidator::class,
                    'options' => [
                        'entityManager' => $this->entityManager,
                        'entity' => $this->environment,
                        'entityClass' => Environment::class,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'description',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 1024
                    ],
                ],
            ],
        ]);
    }
}
