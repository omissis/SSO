<?php
// lib/Agavee/SSO/Server/XmlResponseFormatter.php
namespace Agavee\SSO\Server;

class XmlResponseFormatter implements ResponseFormatterInterface
{
    private $dom;
    private $data;

    public function __construct(array $data = array())
    {
        $this->dom = new \DOMDocument('1.0', 'utf-8');
        $this->data = $data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getDom()
    {
        return $this->dom;
    }

    public function dump()
    {
        $this->format($this->dom, $this->data);
        return $this->dom->saveXml();
    }

    private function format(\DOMNode $node, array $data) {
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                if ('DateTime' === get_class($value)) {
                    $value = $value->format('U');
                }
                $element = $this->dom->createElement($key, (string)$value);
            } elseif (is_array($value)) {
                $element = $this->dom->createElement($key);
                $this->format($element, $value);
            } else {
                if (is_numeric($key)) {
                    $key = rtrim($node->nodeName, 's');
                }
                $element = $this->dom->createElement($key, $value);
            }
            $node->appendChild($element);
        }
    }
}