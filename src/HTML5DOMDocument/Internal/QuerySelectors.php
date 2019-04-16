<?php

namespace IvoPetkov\HTML5DOMDocument\Internal;

use IvoPetkov\HTML5DOMElement;

trait QuerySelectors
{

    /**
     * Returns the first element matching the selector.
     * 
     * @param string $selector A CSS query selector. Available values: *, tagname, tagname#id, #id, tagname.classname, .classname, tagname[attribute-selector] and [attribute-selector].
     * @return HTML5DOMElement|null The result DOMElement or null if not found
     */
    private function internalQuerySelector(string $selector)
    {
        $result = $this->internalQuerySelectorAll($selector, 1);
        return $result->item(0);
    }

    /**
     * Returns a list of document elements matching the selector.
     * 
     * @param string $selector A CSS query selector. Available values: *, tagname, tagname#id, #id, tagname.classname, .classname, tagname[attribute-selector] and [attribute-selector].
     * @param int|null $preferredLimit Preferred maximum number of elements to return.
     * @return DOMNodeList Returns a list of DOMElements matching the criteria.
     * @throws \InvalidArgumentException
     */
    private function internalQuerySelectorAll(string $selector, $preferredLimit = null)
    {
        $selector = trim($selector);
        $walkChildren = function(\DOMNode $context, $tagName, callable $callback) use (&$walkChildren) { // $walkChildren is a lot faster than $this->getElementsByTagName('*') for 300+ elements
            if ($tagName !== null) {
                $children = $context->getElementsByTagName($tagName);
                foreach ($children as $child) {
                    if ($callback($child) === true) {
                        return true;
                    }
                }
            } else {
                foreach ($context->childNodes as $child) {
                    if ($child instanceof \DOMElement) {
                        if ($callback($child) === true) {
                            return true;
                        }
                        if ($walkChildren($child, $tagName, $callback) === true) {
                            return true;
                        }
                    }
                }
            }
        };

        $getElementById = function(\DOMNode $context, $id, $tagName) use (&$walkChildren) {
            if ($context instanceof \DOMDocument) {
                $element = $context->getElementById($id);
                if ($element && ($tagName === null || $element->tagName === $tagName)) {
                    return $element;
                }
            } else {
                $foundElement = null;
                $walkChildren($context, $tagName, function($element) use ($id, &$foundElement) {
                    if ($element->attributes->length > 0 && $element->getAttribute('id') === $id) {
                        $foundElement = $element;
                        return true;
                    }
                });
                return $foundElement;
            }
            return null;
        };

        $simpleSelectors = [];

        // all
        $simpleSelectors['\*'] = function(string $mode, array $match, \DOMNode $context, callable $add = null) use ($walkChildren) {
            if ($mode === 'validate') {
                return true;
            } else {
                $walkChildren($context, null, function($element) use ($add) {
                    if ($add($element)) {
                        return true;
                    }
                });
            }
        };

        // tagname
        $simpleSelectors['[a-z0-9\-]+'] = function(string $mode, array $match, \DOMNode $context, callable $add = null) use ($walkChildren) {
            if ($mode === 'validate') {
                return $context->tagName === $match[0];
            } else {
                $walkChildren($context, $match[0], function($element) use ($add) {
                    if ($add($element)) {
                        return true;
                    }
                });
            }
        };

        // tagname[target] or [target] // Available values for targets: attr, attr="value", attr~="value", attr|="value", attr^="value", attr$="value", attr*="value"
        $simpleSelectors['(?:[a-z0-9\-]*)(?:\[.+?\])'] = function(string $mode, array $match, \DOMNode $context, callable $add = null) use ($walkChildren) {
            $attributeSelectors = explode('][', substr($match[2], 1, -1));
            foreach ($attributeSelectors as $i => $attributeSelector) {
                $attributeSelectorMatches = null;
                if (preg_match('/^(.+?)(=|~=|\|=|\^=|\$=|\*=)\"(.+?)\"$/', $attributeSelector, $attributeSelectorMatches) === 1) {
                    $attributeSelectors[$i] = [
                        'name' => $attributeSelectorMatches[1],
                        'value' => $attributeSelectorMatches[3],
                        'operator' => $attributeSelectorMatches[2]
                    ];
                } else {
                    $attributeSelectors[$i] = [
                        'name' => $attributeSelector
                    ];
                }
            }
            $tagName = strlen($match[1]) > 0 ? $match[1] : null;
            $check = function($element) use ($attributeSelectors) {
                if ($element->attributes->length > 0) {
                    foreach ($attributeSelectors as $attributeSelector) {
                        $isMatch = false;
                        $attributeValue = $element->getAttribute($attributeSelector['name']);
                        if (isset($attributeSelector['value'])) {
                            $valueToMatch = $attributeSelector['value'];
                            switch ($attributeSelector['operator']) {
                                case '=':
                                    if ($attributeValue === $valueToMatch) {
                                        $isMatch = true;
                                    }
                                    break;
                                case '~=':
                                    $words = preg_split("/[\s]+/", $attributeValue);
                                    if (array_search($valueToMatch, $words) !== false) {
                                        $isMatch = true;
                                    }
                                    break;

                                case '|=':
                                    if ($attributeValue === $valueToMatch || strpos($attributeValue, $valueToMatch . '-') === 0) {
                                        $isMatch = true;
                                    }
                                    break;

                                case '^=':
                                    if (strpos($attributeValue, $valueToMatch) === 0) {
                                        $isMatch = true;
                                    }
                                    break;

                                case '$=':
                                    if (substr($attributeValue, -strlen($valueToMatch)) === $valueToMatch) {
                                        $isMatch = true;
                                    }
                                    break;

                                case '*=':
                                    if (strpos($attributeValue, $valueToMatch) !== false) {
                                        $isMatch = true;
                                    }
                                    break;
                            }
                        } else {
                            if ($attributeValue !== '') {
                                $isMatch = true;
                            }
                        }
                        if (!$isMatch) {
                            return false;
                        }
                    }
                    return true;
                }
                return false;
            };
            if ($mode === 'validate') {
                return ($tagName === null ? true : $context->tagName === $tagName) && $check($context);
            } else {
                $walkChildren($context, $tagName, function($element) use ($check, $add) {
                    if ($check($element)) {
                        if ($add($element)) {
                            return true;
                        }
                    }
                });
            }
        };

        // tagname#id or #id
        $simpleSelectors['(?:[a-z0-9\-]*)#(?:.+)'] = function(string $mode, array $match, \DOMNode $context, callable $add = null) use ($getElementById) {
            $tagName = strlen($match[1]) > 0 ? $match[1] : null;
            $id = $match[2];
            if ($mode === 'validate') {
                return ($tagName === null ? true : $context->tagName === $tagName) && $context->getAttribute('id') === $id;
            } else {
                $element = $getElementById($context, $id, $tagName);
                if ($element) {
                    $add($element);
                }
            }
        };

        // tagname.classname, .classname, tagname.classname.classname2, .classname.classname2
        $simpleSelectors['(?:[a-z0-9\-]*)\.(?:.+?)'] = function(string $mode, array $match, \DOMNode $context, callable $add = null) use ($walkChildren) {
            $tagName = strlen($match[1]) > 0 ? $match[1] : null;
            $classesSelector = explode('.', $match[2]);
            if (empty($classesSelector)) {
                return false;
            }
            $check = function($element) use ($classesSelector) {
                if ($element->attributes->length > 0) {
                    $classAttribute = $element->getAttribute('class');
                    $allClassesFound = true;
                    foreach ($classesSelector as $classSelector) {
                        if (!($classAttribute === $classSelector || strpos($classAttribute, $classSelector . ' ') === 0 || substr($classAttribute, -(strlen($classSelector) + 1)) === ' ' . $classSelector || strpos($classAttribute, ' ' . $classSelector . ' ') !== false)) {
                            $allClassesFound = false;
                            break;
                        }
                    }
                    if ($allClassesFound) {
                        return true;
                    }
                }
                return false;
            };
            if ($mode === 'validate') {
                return ($tagName === null ? true : $context->tagName === $tagName) && $check($context);
            } else {
                $walkChildren($context, $tagName, function($element) use ($check, $add) {
                    if ($check($element)) {
                        if ($add($element)) {
                            return true;
                        }
                    }
                });
            }
        };

        $isMatchingElement = function(\DOMNode $context, string $selector) use ($simpleSelectors) {
            foreach ($simpleSelectors as $simpleSelector => $callback) {
                $match = null;
                if (preg_match('/^' . (str_replace('?:', '', $simpleSelector)) . '$/', $selector, $match) === 1) {
                    return call_user_func($callback, 'validate', $match, $context);
                }
            }
        };

        $complexSelectors = [];

        $getMatchingElements = function(\DOMNode $context, string $selector, $preferredLimit = null) use (&$simpleSelectors, &$complexSelectors) {

            $processSelector = function(string $mode, string $selector, $operator = null) use (&$processSelector, $simpleSelectors, $complexSelectors, $context, $preferredLimit) {
                $supportedSimpleSelectors = array_keys($simpleSelectors);
                $supportedSimpleSelectorsExpression = '(?:(?:' . implode(')|(?:', $supportedSimpleSelectors) . '))';
                $supportedSelectors = $supportedSimpleSelectors;
                $supportedComplexOperators = array_keys($complexSelectors);
                if ($operator === null) {
                    $operator = ',';
                    foreach ($supportedComplexOperators as $comprexOperator) {
                        array_unshift($supportedSelectors, '(?:(?:(?:' . $supportedSimpleSelectorsExpression . '\s*\\' . $comprexOperator . '\s*))+' . $supportedSimpleSelectorsExpression . ')');
                    }
                }
                $supportedSelectorsExpression = '(?:(?:' . implode(')|(?:', $supportedSelectors) . '))';

                $vallidationExpression = '/^(?:(?:' . $supportedSelectorsExpression . '\s*\\' . $operator . '\s*))*' . $supportedSelectorsExpression . '$/';
                if (preg_match($vallidationExpression, $selector) !== 1) {
                    return false;
                }
                $selector .= $operator; // append the seprator at the back for easier matching bellow

                $result = [];
                if ($mode === 'execute') {
                    $add = function($element) use ($preferredLimit, &$result) {
                        $found = false;
                        foreach ($result as $addedElement) {
                            if ($addedElement === $element) {
                                $found = true;
                                break;
                            }
                        }
                        if ($found) {
                            return false;
                        }
                        $result[] = $element;
                        if ($preferredLimit !== null && sizeof($result) >= $preferredLimit) {
                            return true;
                        }
                        return false;
                    };
                }

                for ($i = 0; $i < 100000; $i++) {
                    $matches = null;
                    preg_match('/^(?<subselector>' . $supportedSelectorsExpression . ')\s*\\' . $operator . '\s*/', $selector, $matches); // getting the next subselector
                    if (isset($matches['subselector'])) {
                        $subSelector = $matches['subselector'];
                        $selectorFound = false;
                        foreach ($simpleSelectors as $simpleSelector => $callback) {
                            $match = null;
                            if (preg_match('/^' . (str_replace('?:', '', $simpleSelector)) . '$/', $subSelector, $match) === 1) {// if simple selector
                                if ($mode === 'parse') {
                                    $result[] = $match[0];
                                } else {
                                    call_user_func($callback, 'execute', $match, $context, $add);
                                }
                                $selectorFound = true;
                                break;
                            }
                        }
                        if (!$selectorFound) {
                            foreach ($complexSelectors as $comprexOperator => $callback) {
                                $subSelectorParts = $processSelector('parse', $subSelector, $comprexOperator);
                                if ($subSelectorParts !== false) {
                                    call_user_func($callback, $subSelectorParts, $context, $add);
                                    $selectorFound = true;
                                    break;
                                }
                            }
                        }
                        if (!$selectorFound) {
                            throw new \Exception('Internal error for selector "' . $selector . '"!');
                        }
                        $selector = substr($selector, strlen($matches[0])); // remove the matched subselector and continue parsing
                        if (strlen($selector) === 0) {
                            break;
                        }
                    }
                }
                return $result;
            };

            return $processSelector('execute', $selector);
        };

        // div p (space between) - all <p> elements inside <div> elements
        $complexSelectors[' '] = function(array $parts, \DOMNode $context, callable $add = null) use (&$getMatchingElements) {
            $elements = null;
            foreach ($parts as $part) {
                if ($elements === null) {
                    $elements = $getMatchingElements($context, $part);
                } else {
                    $temp = [];
                    foreach ($elements as $element) {
                        $temp = array_merge($temp, $getMatchingElements($element, $part));
                    }
                    $elements = $temp;
                }
            }
            foreach ($elements as $element) {
                $add($element);
            }
        };

        // div > p - all <p> elements where the parent is a <div> element
        $complexSelectors['>'] = function(array $parts, \DOMNode $context, callable $add = null) use (&$getMatchingElements, &$isMatchingElement) {
            $elements = null;
            foreach ($parts as $part) {
                if ($elements === null) {
                    $elements = $getMatchingElements($context, $part);
                } else {
                    $temp = [];
                    foreach ($elements as $element) {
                        foreach ($element->childNodes as $child) {
                            if ($child instanceof \DOMElement && $isMatchingElement($child, $part)) {
                                $temp[] = $child;
                            }
                        }
                    }
                    $elements = $temp;
                }
            }
            foreach ($elements as $element) {
                $add($element);
            }
        };

        // div + p - all <p> elements that are placed immediately after <div> elements
        $complexSelectors['+'] = function(array $parts, \DOMNode $context, callable $add = null) use (&$getMatchingElements, &$isMatchingElement) {
            $elements = null;
            foreach ($parts as $part) {
                if ($elements === null) {
                    $elements = $getMatchingElements($context, $part);
                } else {
                    $temp = [];
                    foreach ($elements as $element) {
                        if ($element->nextSibling !== null && $isMatchingElement($element->nextSibling, $part)) {
                            $temp[] = $element->nextSibling;
                        }
                    }
                    $elements = $temp;
                }
            }
            foreach ($elements as $element) {
                $add($element);
            }
        };

        // p ~ ul -	all <ul> elements that are preceded by a <p> element
        $complexSelectors['~'] = function(array $parts, \DOMNode $context, callable $add = null) use (&$getMatchingElements, &$isMatchingElement) {
            $elements = null;
            foreach ($parts as $part) {
                if ($elements === null) {
                    $elements = $getMatchingElements($context, $part);
                } else {
                    $temp = [];
                    foreach ($elements as $element) {
                        $nextSibling = $element->nextSibling;
                        while ($nextSibling !== null) {
                            if ($isMatchingElement($nextSibling, $part)) {
                                $temp[] = $nextSibling;
                            }
                            $nextSibling = $nextSibling->nextSibling;
                        }
                    }
                    $elements = $temp;
                }
            }
            foreach ($elements as $element) {
                $add($element);
            }
        };

        $result = $getMatchingElements($this, $selector, $preferredLimit);
        if ($result === false) {
            throw new \InvalidArgumentException('Unsupported selector (' . $selector . ')');
        }
        return new \IvoPetkov\HTML5DOMNodeList($result);
    }

}
