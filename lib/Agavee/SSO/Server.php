<?php
// lib/Agavee/SSO/Server.php
namespace Agavee\SSO;

use Agavee\SSO\Factory\TokenFactoryInterface;
use Agavee\SSO\Server\ResponseFormatterInterface;
use Agavee\SSO\Exception\InvalidTokenExcpetion;

class Server
{
    private $tokenFactory;
    private $formatter;
    private $secret;

    public function __construct(TokenFactoryInterface $tokenFactory, $secret, ResponseFormatterInterface $formatter)
    {
        $this->tokenFactory = $tokenFactory;
        $this->formatter    = $formatter;
        $this->secret       = $secret;
    }

    public function formatResponse($data)
    {
        $headers = array('Content-type' =>'text/xml; charset=UTF-8');

        $this->formatter->setData($data);

        return array($headers, $this->formatter->dump());
    }

    public function token($secret, $email, $agent, $ip)
    {
        if ($this->secret !== $secret) {
            throw new InvalidTokenExcpetion('Wrong token');
        }

        return $this->tokenFactory->fromUserData(array(
            'email' => $email,
            'agent' => $agent,
            'ip'    => $ip,
        ));
    }

    public function user()
    {

    }

    public function login()
    {

    }
}