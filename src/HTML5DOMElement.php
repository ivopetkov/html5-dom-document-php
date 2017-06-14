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

    use \IvoPetkov\HTML5DOMDocument\Internal\QuerySelectors;

    /**
     *
     * @var array 
     */
    static private $foundEntitiesCache = [[], []];

    /**
     *
     * @var array 
     */
    static private $newObjectsCache = [];

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
            if ($this->firstChild === null) {
                return '';
            }
            $html = $this->ownerDocument->saveHTML($this);
            $nodeName = $this->nodeName;
            return preg_replace('@^<' . $nodeName . '[^>]*>|</' . $nodeName . '>$@', '', $html);
        } elseif ($name === 'outerHTML') {
            if ($this->firstChild === null) {
                $nodeName = $this->nodeName;
                $attributes = $this->getAttributes();
                $result = '<' . $nodeName . '';
                foreach ($attributes as $name => $value) {
                    $result .= ' ' . $name . '="' . htmlentities($value) . '"';
                }
                if (array_search($nodeName, ['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr']) === false) {
                    $result .= '></' . $nodeName . '>';
                } else {
                    $result .= '/>';
                }
                return $result;
            }
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
            if (!isset(self::$newObjectsCache['html5domdocument'])) {
                self::$newObjectsCache['html5domdocument'] = new \IvoPetkov\HTML5DOMDocument();
            }
            $tmpDoc = clone(self::$newObjectsCache['html5domdocument']);
            $tmpDoc->loadHTML('<body>' . $value . '</body>');
            foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
                $node = $this->ownerDocument->importNode($node, true);
                $this->appendChild($node);
            }
            return;
        } elseif ($name === 'outerHTML') {
            if (!isset(self::$newObjectsCache['html5domdocument'])) {
                self::$newObjectsCache['html5domdocument'] = new \IvoPetkov\HTML5DOMDocument();
            }
            $tmpDoc = clone(self::$newObjectsCache['html5domdocument']);
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

        $value = str_replace(self::$foundEntitiesCache[0], self::$foundEntitiesCache[1], $value);
        if (strstr($value, 'html5-dom-document-internal-entity') !== false) {
            $search = [];
            $replace = [];
            $matches = [];
            preg_match_all('/html5-dom-document-internal-entity([12])-(.*?)-end/', $value, $matches);
            $matches[0] = array_unique($matches[0]);
            foreach ($matches[0] as $i => $match) {
                $search[] = $match;
                $replace[] = html_entity_decode(($matches[1][$i] === '1' ? '&' : '&#') . $matches[2][$i] . ';');
            }
            $value = str_replace($search, $replace, $value);
            self::$foundEntitiesCache[0] = array_merge(self::$foundEntitiesCache[0], $search);
            self::$foundEntitiesCache[1] = array_merge(self::$foundEntitiesCache[1], $replace);
            unset($search);
            unset($replace);
            unset($matches);
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
        $value = parent::getAttribute($name);
        return $value !== '' ? (strstr($value, 'html5-dom-document-internal-entity') !== false ? $this->updateResult($value) : $value) : '';
    }

    /**
     * Returns an array containing all attributes
     * 
     * @return array An associative array containing all attributes
     */
    public function getAttributes()
    {
        $attributes = [];
        foreach ($this->attributes as $attributeName => $attribute) {
            $value = $attribute->value;
            $attributes[$attributeName] = $value !== '' ? (strstr($value, 'html5-dom-document-internal-entity') !== false ? $this->updateResult($value) : $value) : '';
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

    /**
     * Returns the first child element matching the selector
     * 
     * @param string $selector CSS query selector
     * @return \DOMElement|null The result DOMElement or null if not found
     */
    public function querySelector($selector)
    {
        return $this->internalQuerySelector($selector);
    }

    /**
     * Returns a list of children elements matching the selector
     * 
     * @param string $selector CSS query selector
     * @return DOMNodeList Returns a list of DOMElements matching the criteria
     * @throws \InvalidArgumentException
     */
    public function querySelectorAll($selector)
    {
        return $this->internalQuerySelectorAll($selector);
    }

}
