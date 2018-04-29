<?php

namespace Configuration\Form;

use Configuration\Entity\Application;
use Configuration\Validator\EntityByNameExistsValidator;
use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\InputFilter\ArrayInput;
use Zend\InputFilter\InputFilter;

class ApplicationForm extends Form
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Application
     */
    private $application;

    /**
     * ApplicationForm constructor.
     * @param EntityManager $entityManager
     * @param Application|null $application
     */
    public function __construct(EntityManager $entityManager, Application $application = null)
    {
        parent::__construct('application-form');

        $this->entityManager = $entityManager;
        $this->application = $application;

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
                'label' => 'Application Name',
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
            'type' => 'multicheckbox',
            'name' => 'environments',
            'options' => [
                'label' => 'Environment(s)',
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
                        'entity' => $this->application,
                        'entityClass' => Application::class,
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

        $inputFilter->add([
            'class' => ArrayInput::class,
            'name' => 'environments',
            'required' => true,
            'filters' => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'GreaterThan', 'options' => ['min' => 0]]
            ],
        ]);
    }
}
