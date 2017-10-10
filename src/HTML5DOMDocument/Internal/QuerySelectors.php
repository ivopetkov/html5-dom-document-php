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
     * @return \DOMNodeList Returns a list of DOMElements matching the criteria
     * @throws \InvalidArgumentException
     */
    private function internalQuerySelectorAll(string $selector, $preferredLimit = null)
    {
        $walkChildren = function($element, $callback) use (&$walkChildren) { // $walkChildren is a lot faster than $this->getElementsByTagName('*') for 300+ elements
            foreach ($element->childNodes as $child) {
                if ($child instanceof \DOMElement) {
                    if ($callback($child) === true) {
                        return true;
                    }
                    if ($walkChildren($child, $callback) === true) {
                        return true;
                    }
                }
            }
        };

        $getElementById = function($id) use (&$walkChildren) {
            if ($this instanceof \DOMDocument) {
                return $this->getElementById($id);
            } else {
                $foundElement = null;
                $walkChildren($this, function($element) use ($id, &$foundElement) {
                    if ($element->attributes->length > 0 && $element->getAttribute('id') === $id) {
                        $foundElement = $element;
                        return true;
                    }
                });
                return $foundElement;
            }
            return null;
        };

        $matches = null;
        if ($selector === '*') { // all
            $result = [];
            $walkChildren($this, function($element) use (&$result) {
                $result[] = $element;
            });
            return new \IvoPetkov\HTML5DOMNodeList($result);
        } elseif (preg_match('/^[a-z0-9]+$/', $selector) === 1) { // tagname
            $result = [];
            $walkChildren($this, function($element) use (&$result, $selector) {
                if ($element->tagName === $selector) {
                    $result[] = $element;
                }
            });
            return new \IvoPetkov\HTML5DOMNodeList($result);
        } elseif (preg_match('/^([a-z0-9]*)\[(.+)\=\"(.+)\"\]$/', $selector, $matches) === 1) { // tagname[attribute="value"] or [attribute="value"]
            $result = [];
            $tagName = strlen($matches[1]) > 0 ? $matches[1] : null;
            $walkChildren($this, function($element) use (&$result, $tagName, $preferredLimit, $matches) {
                if ($tagName === null || $element->tagName === $tagName) {
                    if ($element->attributes->length > 0 && $element->getAttribute($matches[2]) === $matches[3]) {
                        $result[] = $element;
                        if ($preferredLimit !== null && sizeof($result) >= $preferredLimit) {
                            return true;
                        }
                    }
                }
            });
            return new \IvoPetkov\HTML5DOMNodeList($result);
        } elseif (preg_match('/^([a-z0-9]*)#(.+)$/', $selector, $matches) === 1) { // tagname#id or #id
            $tagName = strlen($matches[1]) > 0 ? $matches[1] : null;
            $idSelector = $matches[2];
            $element = $getElementById($idSelector);
            if ($element && ($tagName === null || $element->tagName === $tagName)) {
                return new \IvoPetkov\HTML5DOMNodeList([$element]);
            }
            return new \IvoPetkov\HTML5DOMNodeList();
        } elseif (preg_match('/^([a-z0-9]*)\.(.+)$/', $selector, $matches) === 1) { // tagname.classname or .classname
            $parts = explode('.', $selector, 2);
            $tagName = strlen($matches[1]) > 0 ? $matches[1] : null;
            $classSelector = $matches[2];
            $result = [];
            $walkChildren($this, function($element) use (&$result, $tagName, $classSelector, $preferredLimit) {
                if (($tagName === null || $element->tagName === $tagName) && $element->attributes->length > 0) {
                    $classAttribute = $element->getAttribute('class');
                    if ($classAttribute === $classSelector || strpos($classAttribute, $classSelector . ' ') === 0 || substr($classAttribute, -(strlen($classSelector) + 1)) === ' ' . $classSelector || strpos($classAttribute, ' ' . $classSelector . ' ') !== false) {
                        $result[] = $element;
                        if ($preferredLimit !== null && sizeof($result) >= $preferredLimit) {
                            return true;
                        }
                    }
                }
            });
            return new \IvoPetkov\HTML5DOMNodeList($result);
        }
        throw new \InvalidArgumentException('Unsupported selector');
    }

}
