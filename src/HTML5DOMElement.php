<?php

/*
 * HTML5 DOMDocument PHP library (extends DOMDocument)
 * https://github.com/ivopetkov/html5-dom-document-php
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov;

use IvoPetkov\HTML5DOMDocument\Internal\QuerySelectors;

/**
 * Represents a live (can be manipulated) representation of an element in a HTML5 document.
 * 
 * @property string $innerHTML The HTML code inside the element.
 * @property string $outerHTML The HTML code for the element including the code inside.
 * @property \IvoPetkov\HTML5DOMTokenList $classList A collection of the class attributes of the element.
 */
class HTML5DOMElement extends \DOMElement
{

    use QuerySelectors;

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

    /*
     * 
     * @var HTML5DOMTokenList
     */
    private $classList = null;

    /**
     * Returns the value for the property specified.
     *
     * @param string $name
     * @return string
     * @throws \Exception
     */
    public function __get(string $name)
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
        } elseif ($name === 'classList') {
            if ($this->classList === null) {
                $this->classList = new HTML5DOMTokenList($this, 'class');
            }
            return $this->classList;
        }
        throw new \Exception('Undefined property: HTML5DOMElement::$' . $name);
    }

    /**
     * Sets the value for the property specified.
     *
     * @param string $name
     * @param string $value
     * @throws \Exception
     */
    public function __set(string $name, $value)
    {
        if ($name === 'innerHTML') {
            while ($this->hasChildNodes()) {
                $this->removeChild($this->firstChild);
            }
            if (!isset(self::$newObjectsCache['html5domdocument'])) {
                self::$newObjectsCache['html5domdocument'] = new \IvoPetkov\HTML5DOMDocument();
            }
            $tmpDoc = clone (self::$newObjectsCache['html5domdocument']);
            $tmpDoc->loadHTML('<body>' . $value . '</body>', HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
            foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
                $node = $this->ownerDocument->importNode($node, true);
                $this->appendChild($node);
            }
            return;
        } elseif ($name === 'outerHTML') {
            if (!isset(self::$newObjectsCache['html5domdocument'])) {
                self::$newObjectsCache['html5domdocument'] = new \IvoPetkov\HTML5DOMDocument();
            }
            $tmpDoc = clone (self::$newObjectsCache['html5domdocument']);
            $tmpDoc->loadHTML('<body>' . $value . '</body>', HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
            foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
                $node = $this->ownerDocument->importNode($node, true);
                $this->parentNode->insertBefore($node, $this);
            }
            $this->parentNode->removeChild($this);
            return;
        } elseif ($name === 'classList') {
            $this->setAttribute('class', $value);
            return;
        }
        throw new \Exception('Undefined property: HTML5DOMElement::$' . $name);
    }

    /**
     * Updates the result value before returning it.
     *
     * @param string $value
     * @return string The updated value
     */
    private function updateResult(string $value): string
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
     * Returns the updated nodeValue Property
     * 
     * @return string The updated $nodeValue
     */
    public function getNodeValue(): string
    {
        return $this->updateResult($this->nodeValue);
    }

    /**
     * Returns the updated $textContent Property
     * 
     * @return string The updated $textContent
     */
    public function getTextContent(): string
    {
        return $this->updateResult($this->textContent);
    }

    /**
     * Returns the value for the attribute name specified.
     *
     * @param string $name The attribute name.
     * @return string The attribute value.
     * @throws \InvalidArgumentException
     */
    public function getAttribute($name): string
    {
        if ($this->attributes->length === 0) { // Performance optimization
            return '';
        }
        $value = parent::getAttribute($name);
        return $value !== '' ? (strstr($value, 'html5-dom-document-internal-entity') !== false ? $this->updateResult($value) : $value) : '';
    }

    /**
     * Returns an array containing all attributes.
     *
     * @return array An associative array containing all attributes.
     */
    public function getAttributes(): array
    {
        $attributes = [];
        foreach ($this->attributes as $attributeName => $attribute) {
            $value = $attribute->value;
            $attributes[$attributeName] = $value !== '' ? (strstr($value, 'html5-dom-document-internal-entity') !== false ? $this->updateResult($value) : $value) : '';
        }
        return $attributes;
    }

    /**
     * Returns the element outerHTML.
     *
     * @return string The element outerHTML.
     */
    public function __toString(): string
    {
        return $this->outerHTML;
    }

    /**
     * Returns the first child element matching the selector.
     *
     * @param string $selector A CSS query selector. Available values: *, tagname, tagname#id, #id, tagname.classname, .classname, tagname.classname.classname2, .classname.classname2, tagname[attribute-selector], [attribute-selector], "div, p", div p, div > p, div + p and p ~ ul.
     * @return HTML5DOMElement|null The result DOMElement or null if not found.
     * @throws \InvalidArgumentException
     */
    public function querySelector(string $selector)
    {
        return $this->internalQuerySelector($selector);
    }

    /**
     * Returns a list of children elements matching the selector.
     *
     * @param string $selector A CSS query selector. Available values: *, tagname, tagname#id, #id, tagname.classname, .classname, tagname.classname.classname2, .classname.classname2, tagname[attribute-selector], [attribute-selector], "div, p", div p, div > p, div + p and p ~ ul.
     * @return HTML5DOMNodeList Returns a list of DOMElements matching the criteria.
     * @throws \InvalidArgumentException
     */
    public function querySelectorAll(string $selector)
    {
        return $this->internalQuerySelectorAll($selector);
    }
}
