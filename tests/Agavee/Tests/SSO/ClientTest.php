<?php

namespace Agavee\Tests\SSO;

use Agavee\SSO\Exception\InvalidTokenException;
use Agavee\SSO\Client;
use \Mockery as m;

class ClientTest extends \PHPUnit_Framework_Testcase
{
    private $emptyServerUrl = '';
    private $wrongServerUrl = 'wrong';
    private $correctServerUrl = 'http://correct.com/';
    private $loginParams = array(
        'email'        => 'undefined@test.com',
        'password'     => 'undefinedPassword',
        'token'        => 'undefinedToken',
        'successUrl' => 'http://success.com/callback',
        'failureUrl'   => 'http://failure.com/callback',
    );
    private $logoutParams = array(
        'token'        => 'undefinedToken',
        'successUrl' => 'http://success.com/callback',
        'failureUrl'   => 'http://failure.com/callback',
    );

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid Server URL
     */
    public function testClientDoesntAcceptEmptyServerUrl()
    {
        $client = new Client($this->emptyServerUrl);
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Malformed Server URL: missing
     */
    public function testClientDoesntAcceptInvalidServerUrl()
    {
        $client = new Client($this->wrongServerUrl);
    }

    public function testClientSuccessfulCreation()
    {
        $client = new Client($this->correctServerUrl);

        $this->assertInstanceOf('Agavee\\SSO\\Client', $client);
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage The following parameters are missing:
     */
    public function testClientGetLoginUrlWithEmptyParams()
    {
        $client = new Client($this->correctServerUrl);

        $client->getLoginUrl();
    }

    public function testClientGetLoginUrlWithAllParams()
    {
        $client = new Client($this->correctServerUrl);

        $loginUrl = $client->getLoginUrl($this->loginParams);

        $expectedLoginUrl =
            $this->correctServerUrl . 'sso/login'
            . '?email=' . urlencode($this->loginParams['email'])
            . '&password=' . urlencode($this->loginParams['password'])
            . '&token=' . urlencode($this->loginParams['token'])
            . '&successUrl=' . urlencode($this->loginParams['successUrl'])
            . '&failureUrl=' . urlencode($this->loginParams['failureUrl']);

        $this->assertSame($expectedLoginUrl, $loginUrl);
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage The following parameters are missing:
     */
    public function testClientGetLogoutUrlWithEmptyParams()
    {
        $client = new Client($this->correctServerUrl);

        $client->getLogoutUrl();
    }

    public function testClientGetLogoutUrlWithAllParams()
    {
        $client = new Client($this->correctServerUrl);

        $logoutUrl = $client->getLogoutUrl($this->logoutParams);

        $expectedLogoutUrl =
            $this->correctServerUrl . 'sso/logout'
            . '?token=' . urlencode($this->loginParams['token'])
            . '&successUrl=' . urlencode($this->loginParams['successUrl'])
            . '&failureUrl=' . urlencode($this->loginParams['failureUrl']);

        $this->assertSame($expectedLogoutUrl, $logoutUrl);
    }
}