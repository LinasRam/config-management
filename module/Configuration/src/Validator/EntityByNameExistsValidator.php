<?php

namespace Configuration\Validator;

use Zend\Validator\AbstractValidator;

class EntityByNameExistsValidator extends AbstractValidator
{
    /**
     * Available validator options.
     * @var array
     */
    protected $options = [
        'entityManager' => null,
        'entity' => null,
        'entityClass' => null,
    ];

    const NOT_SCALAR = 'notScalar';
    const ENTITY_EXISTS = 'entityExists';

    /**
     * Validation failure messages.
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SCALAR => "Must be a scalar value",
        self::ENTITY_EXISTS => "Entity with such name already exists"
    );

    /**
     * Constructor.
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['entityManager'])) {
                $this->options['entityManager'] = $options['entityManager'];
            }
            if (isset($options['entity'])) {
                $this->options['entity'] = $options['entity'];
            }
            if (isset($options['entityClass'])) {
                $this->options['entityClass'] = $options['entityClass'];
            }
        }

        parent::__construct($options);
    }

    /**
     * Check if entity exists.
     */
    public function isValid($value)
    {
        if (!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        $entityManager = $this->options['entityManager'];

        $entity = $entityManager->getRepository($this->options['entityClass'])
            ->findOneByName($value);

        if ($this->options['entity'] == null) {
            $isValid = ($entity == null);
        } else {
            if ($this->options['entity']->getName() != $value && $entity != null) {
                $isValid = false;
            } else {
                $isValid = true;
            }
        }

        if (!$isValid) {
            $this->error(self::ENTITY_EXISTS);
        }

        return $isValid;
    }
}
