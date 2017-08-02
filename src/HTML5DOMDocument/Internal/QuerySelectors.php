<?php

namespace IvoPetkov\HTML5DOMDocument\Internal;

trait QuerySelectors
{

    /**
     * Returns the first element matching the selector
     * 
     * @param string $selector CSS query selector
     * @return \DOMElement|null The result DOMElement or null if not found
     */
    private function internalQuerySelector(string $selector)
    {
        $result = $this->internalQuerySelectorAll($selector, 1);
        return $result->item(0);
    }

    /**
     * Returns a list of document elements matching the selector
     * 
     * @param string $selector CSS query selector
     * @param int|null $preferredLimit Preferred maximum number of elements to return
     * @return DOMNodeList Returns a list of DOMElements matching the criteria
     * @throws \InvalidArgumentException
     */
    private function internalQuerySelectorAll(string $selector, $preferredLimit = null)
    {
        $getElementById = function($id) {
            if ($this instanceof \DOMDocument) {
                return $this->getElementById($id);
            } else {
                $elements = $this->getElementsByTagName('*');
                foreach ($elements as $element) {
                    if ($element->attributes->length === 0) { // performance optimization
                        continue;
                    }
                    if ($element->getAttribute('id') === $id) {
                        return $element;
                    }
                }
            }
            return null;
        };

        $matches = null;
        if ($selector === '*') { // all
            return $this->getElementsByTagName('*');
        } elseif (preg_match('/^[a-z0-9]+$/', $selector) === 1) { // tagname
            return $this->getElementsByTagName($selector);
        } elseif (preg_match('/^([a-z0-9]*)\[(.+)\=\"(.+)\"\]$/', $selector, $matches) === 1) { // [attribute="value"] or tagname[attribute="value"]
            $result = [];
            $elements = strlen($matches[1]) > 0 ? $this->getElementsByTagName($matches[1]) : $this->getElementsByTagName('*');
            foreach ($elements as $element) {
                if ($element->attributes->length === 0) { // performance optimization
                    continue;
                }
                if ($element->getAttribute($matches[2]) === $matches[3]) {
                    $result[] = $element;
                    if ($preferredLimit !== null && sizeof($result) >= $preferredLimit) {
                        break;
                    }
                }
            }
            return new \IvoPetkov\HTML5DOMNodeList($result);
        } elseif (preg_match('/^[a-z0-9]+#.+$/', $selector) === 1) { // tagname#id
            $parts = explode('#', $selector, 2);
            $element = $getElementById($parts[1]);
            if ($element && $element->tagName === $parts[0]) {
                return new \IvoPetkov\HTML5DOMNodeList([$element]);
            }
            return new \IvoPetkov\HTML5DOMNodeList();
        } elseif (preg_match('/^[a-z0-9]+\..+$/', $selector) === 1) { // tagname.classname
            $parts = explode('.', $selector, 2);
            $result = [];
            $selectorClass = $parts[1];
            $elements = $this->getElementsByTagName($parts[0]);
            foreach ($elements as $element) {
                $classAttribute = $element->getAttribute('class');
                if ($classAttribute === $selectorClass || strpos($classAttribute, $selectorClass . ' ') === 0 || substr($classAttribute, -(strlen($selectorClass) + 1)) === ' ' . $selectorClass || strpos($classAttribute, ' ' . $selectorClass . ' ') !== false) {
                    $result[] = $element;
                    if ($preferredLimit !== null && sizeof($result) >= $preferredLimit) {
                        break;
                    }
                }
            }
            return new \IvoPetkov\HTML5DOMNodeList($result);
        } elseif (substr($selector, 0, 1) === '#') { // #id
            $element = $getElementById(substr($selector, 1));
            return $element !== null ? new \IvoPetkov\HTML5DOMNodeList([$element]) : new \IvoPetkov\HTML5DOMNodeList();
        } elseif (substr($selector, 0, 1) === '.') { // .classname
            $elements = $this->getElementsByTagName('*');
            $result = [];
            $selectorClass = substr($selector, 1);
            foreach ($elements as $element) {
                if ($element->attributes->length === 0) { // performance optimization
                    continue;
                }
                $classAttribute = $element->getAttribute('class');
                if ($classAttribute === $selectorClass || strpos($classAttribute, $selectorClass . ' ') === 0 || substr($classAttribute, -(strlen($selectorClass) + 1)) === ' ' . $selectorClass || strpos($classAttribute, ' ' . $selectorClass . ' ') !== false) {
                    $result[] = $element;
                    if ($preferredLimit !== null && sizeof($result) >= $preferredLimit) {
                        break;
                    }
                }
            }
            return new \IvoPetkov\HTML5DOMNodeList($result);
        }
        throw new \InvalidArgumentException('Unsupported selector');
    }

}
