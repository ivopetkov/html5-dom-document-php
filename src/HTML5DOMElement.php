<?php

/*
 * HTML5 DOMDocument PHP library (extends DOMDocument)
 * https://github.com/ivopetkov/html5-dom-document-php
 * Copyright 2016, Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov;

/**
 * @property-read string $innerHTML The HTML code inside the element
 * @property-read string $outerHTML The HTML code for the element including the code inside
 */
class HTML5DOMElement extends \DOMElement
{

    /**
     * Returns the value for the property specified
     * 
     * @param string $name The name of the property
     * @return string The value of the property specified
     * @throws \Exception
     */
    public function __get($name)
    {
        if ($name === 'innerHTML') {
            $html = $this->ownerDocument->saveHTML($this);
            $nodeName = $this->nodeName;
            return preg_replace('@^<' . $nodeName . '[^>]*>|</' . $nodeName . '>$@', '', $html);
        } elseif ($name === 'outerHTML') {
            return $this->ownerDocument->saveHTML($this);
        }
        throw new \Exception('Undefined property: HTML5DOMElement::$' . $name);
    }

    /**
     * Sets the value for the property specified
     * 
     * @param string $name
     * @param string $value
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('The value argument must be of type string');
        }
        if ($name === 'innerHTML') {
            while ($this->hasChildNodes()) {
                $this->removeChild($this->firstChild);
            }
            $tmpDoc = new \IvoPetkov\HTML5DOMDocument();
            $tmpDoc->loadHTML('<body>' . $value . '</body>');
            foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
                $node = $this->ownerDocument->importNode($node, true);
                $this->appendChild($node);
            }
            return;
        } elseif ($name === 'outerHTML') {
            $tmpDoc = new \IvoPetkov\HTML5DOMDocument();
            $tmpDoc->loadHTML('<body>' . $value . '</body>');
            foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
                $node = $this->ownerDocument->importNode($node, true);
                $this->parentNode->insertBefore($node, $this);
            }
            $this->parentNode->removeChild($this);
            return;
        }
        throw new \Exception('Undefined property: HTML5DOMElement::$' . $name);
    }

    /**
     * Updates the result value before returning it
     * 
     * @param string $value
     * @return string The updated value
     */
    private function updateResult($value)
    {
        $matches = [];
        preg_match_all('/html5-dom-document-internal-entity1-(.*?)-end/', $value, $matches);
        foreach ($matches[0] as $i => $match) {
            $value = str_replace($match, html_entity_decode('&' . $matches[1][$i] . ';'), $value);
        }
        $matches = [];
        preg_match_all('/html5-dom-document-internal-entity2-(.*?)-end/', $value, $matches);
        foreach ($matches[0] as $i => $match) {
            $value = str_replace($match, html_entity_decode('&#' . $matches[1][$i] . ';'), $value);
        }
        return $value;
    }

    /**
     * Returns the value for the attribute name specified
     * 
     * @param string $name The attribute name
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getAttribute($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('The name argument must be of type string');
        }
        return $this->updateResult(parent::getAttribute($name));
    }

    /**
     * Returns an array containing all attributes
     * 
     * @return array An associative array containing all attributes
     */
    public function getAttributes()
    {
        $attributesCount = $this->attributes->length;
        $attributes = [];
        for ($i = 0; $i < $attributesCount; $i++) {
            $attribute = $this->attributes->item($i);
            $attributes[$attribute->name] = $this->updateResult($attribute->value);
        }
        return $attributes;
    }

    /**
     * Returns the element outerHTML
     * 
     * @return string The element outerHTML
     */
    public function __toString()
    {
        return $this->outerHTML;
    }

}
