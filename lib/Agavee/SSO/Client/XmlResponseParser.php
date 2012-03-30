<?php
// lib/Agavee/SSO/Client/XmlResponseParser.php
namespace Agavee\SSO\Client;

class XmlResponseParser
{
    private $dom;

    public function __construct($responseBody)
    {
        $this->dom = new \DOMDocument('1.0', 'utf-8');
        $this->loadResponseBody($responseBody);
    }

    public function loadResponseBody($responseBody)
    {
        $this->dom->loadXml($responseBody);
    }

    public function getDom()
    {
        return $this->dom;
    }

    public function toObject()
    {
        return $this->parse($this->dom->firstChild, true);
    }

    public function toArray()
    {
        return $this->parse($this->dom->firstChild, false);
    }

    private function parse(\DOMNode $parent, $toObject = false) {
        $userdata = array();
        foreach ($parent->childNodes as $node) {
            if ($node->hasChildNodes() && 'DOMText' !== get_class($node->firstChild)) {
                $userdata[$node->nodeName] = $this->parse($node, $toObject);
            } else {
                if ('DOMText' === get_class($node)) {
                    $userdata = $node->nodeValue;
                } else {
                    $userdata[$node->nodeName] = $node->nodeValue;
                }
            }
        }
        return $toObject ? (object)$userdata : $userdata;
    }
}