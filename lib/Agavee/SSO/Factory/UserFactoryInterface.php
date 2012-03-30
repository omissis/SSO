<?php
// lib/Agavee/SSO/Factory/UserFactoryInterface.php
namespace Agavee\SSO\Factory;

interface UserFactoryInterface
{
    public function fromStdClass(\stdClass $userdata);

    public function fromArray(array $userdata);

    /**
     * Return the name of the class returned by the factory methods
     *
     * @return string
     *   eg: Acme\Bundle\MyBundle\Entity\User
     */
    public function getClass();
}