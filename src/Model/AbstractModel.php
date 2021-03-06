<?php

namespace Terminal42\ActiveCollabApi\Model;

use Terminal42\ActiveCollabApi\Repository\AbstractRepository;
use Terminal42\ActiveCollabApi\Repository\Users;

abstract class AbstractModel
{
    use RepositoryAwareTrait;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @param array              $data
     * @param AbstractRepository $repository
     */
    public function __construct(array $data = [], AbstractRepository $repository = null)
    {
        $this->data = $data;

        if (null !== $repository) {
            $this->setRepository($repository);
        }
    }

    /**
     * Get a model property
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        $varValue = $this->data[$key];

        if (is_object($varValue) && $varValue->class != '') {
            switch ($varValue->class) {

                // Convert to PHP DateTime
                case 'DateTimeValue':
                    // @todo add timezone information if available
                    $varValue = \DateTime::createFromFormat('U', $varValue->timestamp);
                    break;

                // Convert to User object
                case 'Administrator':
                    $varValue = new User($varValue, new Users($this->getRepository()->getApiClient()));
                    break;
            }
        }

        return $varValue;
    }

    /**
     * Set a model property
     * @param $key
     * @param $value
     * @return $this
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Check if property is set
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }
}