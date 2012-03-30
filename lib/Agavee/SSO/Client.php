<?php
// lib/Agavee/SSO/Client.php
namespace Agavee\SSO;

use Agavee\SSO\Client\XmlResponseParser;

class Client
{
    private $serverUrl;

    public function __construct($serverUrl)
    {
        $this->serverUrl = rtrim($serverUrl, '/') . '/';
    }

    public function getToken($request, $email, $secret)
    {
        list($ret, $body) = $this->request('sso/token', array(
            'secret' => $secret,
            'email'  => $email,
            'agent'  => $request->server->get('HTTP_USER_AGENT'),
            'ip'     => $request->getClientIp(),
        ));

        if (200 != $ret || empty($body)) {
            // #fail
            return null;
        }

        $parser = new XmlResponseParser($body);

        return $parser->toArray();
    }

    public function getUser($property, $username, $secret)
    {
        list($ret, $body) = $this->request('sso/user', array(
            $property => $username,
            'secret'  => $secret,
        ));

        if (200 != $ret || empty($body)) {
            // #fail
            return null;
        }

        $parser = new XmlResponseParser($body);

        return $parser->toObject();
    }

    public function getLoginUrl(array $params = array())
    {
        $requiredParams = array('email', 'password', 'token', 'successUrl', 'failureUrl');

        $this->checkParams($requiredParams, $params);

        return $this->getUrl('sso/login', $params);
    }

    public function getLogoutUrl(array $params = array())
    {
        $requiredParams = array('token', 'successUrl', 'failureUrl');

        $this->checkParams($requiredParams, $params);

        return $this->getUrl('sso/logout', $params);
    }

    private function getUrl($path, $params)
    {
        return $this->serverUrl . $path . '?' . vsprintf(implode('=%s&', array_keys($params)) . '=%s', $params);
    }

    private function checkParams(array $requiredParams, array &$params)
    {
        $missingParameters = array_diff($requiredParams, array_keys($params));

        if (!empty($missingParameters)) {
            throw new \InvalidArgumentException(
                'The following parameters are missing: ' . implode(', ', $missingParameters)
            );
        }

        array_walk($params, function($value, $key) use (&$params) {
            $params[$key] = urlencode($value);
        });
    }

    /**
     * Execute a command on SSO server.
     *
     * @param string $cmd  Command
     * @param array  $vars Post variables
     *
     * @return array
     */
    private function request($cmd, $vars = null)
    {
        $curl = curl_init($this->serverUrl . $cmd);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if (isset($vars)) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $vars);
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $body = curl_exec($curl);
        $ret = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (curl_errno($curl) != 0) {
            throw new \Exception("SSO failure: HTTP request to server failed. " . curl_error($curl));
        }

        return array($ret, $body);
    }
}