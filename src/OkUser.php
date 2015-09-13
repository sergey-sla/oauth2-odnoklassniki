<?php
/**
 * Created by PhpStorm.
 * User: rmu
 * Date: 14.09.15
 * Time: 1:08
 */

namespace Aego\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class OkUser implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param  array $response
     */
    public function __construct(array $response)
    {
        $this->data = $response;
    }

    /**
     * Returns the identifier of the authorized resource owner.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getField('uid');
    }

    public function getEmail()
    {
        return $this->getField('email');
    }

    public function getName()
    {
        return $this->getField('name');
    }

    public function getCity()
    {
        return $this->getField('city');
    }

    public function getGender()
    {
        return $this->getField('sex');
    }

    /**
     * Returns all the data obtained about the user.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Returns a field from the Graph node data.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    private function getField($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
}