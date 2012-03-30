<?php
// lib/Agavee/SSO/Factory/TokenFactoryInterface.php
namespace Agavee\SSO\Factory;

interface TokenFactoryInterface
{
    public function fromUserData(array $userdata);

    /**
     * Return the name of the class returned by the factory methods
     *
     * @return string
     *   eg: Acme\Bundle\MyBundle\Entity\Token
     */
    public function getClass();
}