<?php

namespace Configuration\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ConfigurationForm extends Form
{
    /**
     * ConfigurationForm constructor.
     */
    public function __construct()
    {
        parent::__construct('configuration-form');

        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    protected function addElements()
    {
        $this->add([
            'type' => 'text',
            'name' => 'key',
            'attributes' => [
                'id' => 'key'
            ],
            'options' => [
                'label' => 'Key',
            ],
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'value',
            'attributes' => [
                'id' => 'value'
            ],
            'options' => [
                'label' => 'Value',
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
            'name' => 'key',
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
            ],
        ]);

        $inputFilter->add([
            'name' => 'value',
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
            ],
        ]);
    }
}
