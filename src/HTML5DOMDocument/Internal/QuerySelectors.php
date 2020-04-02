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

        $cache = [];
        $walkChildren = function (\DOMNode $context, $tagNames, callable $callback) use (&$cache) {
            if (!empty($tagNames)) {
                $children = [];
                foreach ($tagNames as $tagName) {
                    $elements = $context->getElementsByTagName($tagName);
                    foreach ($elements as $element) {
                        $children[] = $element;
                    }
                }
            } else {
                $getChildren = function () use ($context) {
                    $result = [];
                    $process = function (\DOMNode $node) use (&$process, &$result) {
                        foreach ($node->childNodes as $child) {
                            if ($child instanceof \DOMElement) {
                                $result[] = $child;
                                $process($child);
                            }
                        }
                    };
                    $process($context);
                    return $result;
                };
                if ($this === $context) {
                    $cacheKey = 'walk_children';
                    if (!isset($cache[$cacheKey])) {
                        $cache[$cacheKey] = $getChildren();
                    }
                    $children = $cache[$cacheKey];
                } else {
                    $children = $getChildren();
                }
            }
            foreach ($children as $child) {
                if ($callback($child) === true) {
                    return true;
                }
            }
        };

        $getElementById = function (\DOMNode $context, $id, $tagName) use (&$walkChildren) {
            if ($context instanceof \DOMDocument) {
                $element = $context->getElementById($id);
                if ($element && ($tagName === null || $element->tagName === $tagName)) {
                    return $element;
                }
            } else {
                $foundElement = null;
                $walkChildren($context, $tagName !== null ? [$tagName] : null, function ($element) use ($id, &$foundElement) {
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
        $simpleSelectors['\*'] = function (string $mode, array $matches, \DOMNode $context, callable $add = null) use ($walkChildren) {
            if ($mode === 'validate') {
                return true;
            } else {
                $walkChildren($context, [], function ($element) use ($add) {
                    if ($add($element)) {
                        return true;
                    }
                });
            }
        };

        // tagname
        $simpleSelectors['[a-zA-Z0-9\-]+'] = function (string $mode, array $matches, \DOMNode $context, callable $add = null) use ($walkChildren) {
            $tagNames = [];
            foreach ($matches as $match) {
                $tagNames[] = strtolower($match[0]);
            }
            if ($mode === 'validate') {
                return array_search($context->tagName, $tagNames) !== false;
            }
            $walkChildren($context, $tagNames, function ($element) use ($add) {
                if ($add($element)) {
                    return true;
                }
            });
        };

        // tagname[target] or [target] // Available values for targets: attr, attr="value", attr~="value", attr|="value", attr^="value", attr$="value", attr*="value"
        $simpleSelectors['(?:[a-zA-Z0-9\-]*)(?:\[.+?\])'] = function (string $mode, array $matches, \DOMNode $context, callable $add = null) use ($walkChildren) {
            $run = function ($match) use ($mode, $context, $add, $walkChildren) {
                $attributeSelectors = explode('][', substr($match[2], 1, -1));
                foreach ($attributeSelectors as $i => $attributeSelector) {
                    $attributeSelectorMatches = null;
                    if (preg_match('/^(.+?)(=|~=|\|=|\^=|\$=|\*=)\"(.+?)\"$/', $attributeSelector, $attributeSelectorMatches) === 1) {
                        $attributeSelectors[$i] = [
                            'name' => strtolower($attributeSelectorMatches[1]),
                            'value' => $attributeSelectorMatches[3],
                            'operator' => $attributeSelectorMatches[2]
                        ];
                    } else {
                        $attributeSelectors[$i] = [
                            'name' => $attributeSelector
                        ];
                    }
                }
                $tagName = strlen($match[1]) > 0 ? strtolower($match[1]) : null;
                $check = function ($element) use ($attributeSelectors) {
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
                    $walkChildren($context, $tagName !== null ? [$tagName] : null, function ($element) use ($check, $add) {
                        if ($check($element)) {
                            if ($add($element)) {
                                return true;
                            }
                        }
                    });
                }
            };
            // todo optimize
            foreach ($matches as $match) {
                if ($mode === 'validate') {
                    if ($run($match)) {
                        return true;
                    }
                } else {
                    $run($match);
                }
            }
            if ($mode === 'validate') {
                return false;
            }
        };

        // tagname#id or #id
        $simpleSelectors['(?:[a-zA-Z0-9\-]*)#(?:[a-zA-Z0-9\-\_]+?)'] = function (string $mode, array $matches, \DOMNode $context, callable $add = null) use ($getElementById) {
            $run = function ($match) use ($mode, $context, $add, $getElementById) {
                $tagName = strlen($match[1]) > 0 ? strtolower($match[1]) : null;
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
            // todo optimize
            foreach ($matches as $match) {
                if ($mode === 'validate') {
                    if ($run($match)) {
                        return true;
                    }
                } else {
                    $run($match);
                }
            }
            if ($mode === 'validate') {
                return false;
            }
        };

        // tagname.classname, .classname, tagname.classname.classname2, .classname.classname2
        $simpleSelectors['(?:[a-zA-Z0-9\-]*)\.(?:[a-zA-Z0-9\-\_\.]+?)'] = function (string $mode, array $matches, \DOMNode $context, callable $add = null) use ($walkChildren) {
            $rawData = []; // Array containing [tag, classnames]
            $tagNames = [];
            foreach ($matches as $match) {
                $tagName = strlen($match[1]) > 0 ? $match[1] : null;
                $classes = explode('.', $match[2]);
                if (empty($classes)) {
                    continue;
                }
                $rawData[] = [$tagName, $classes];
                if ($tagName !== null) {
                    $tagNames[] = $tagName;
                }
            }
            $check = function ($element) use ($rawData) {
                if ($element->attributes->length > 0) {
                    $classAttribute = ' ' . $element->getAttribute('class') . ' ';
                    $tagName = $element->tagName;
                    foreach ($rawData as $rawMatch) {
                        if ($rawMatch[0] !== null && $tagName !== $rawMatch[0]) {
                            continue;
                        }
                        $allClassesFound = true;
                        foreach ($rawMatch[1] as $class) {
                            if (strpos($classAttribute, ' ' . $class . ' ') === false) {
                                $allClassesFound = false;
                                break;
                            }
                        }
                        if ($allClassesFound) {
                            return true;
                        }
                    }
                }
                return false;
            };
            if ($mode === 'validate') {
                return $check($context);
            }
            $walkChildren($context, $tagNames, function ($element) use ($check, $add) {
                if ($check($element)) {
                    if ($add($element)) {
                        return true;
                    }
                }
            });
        };

        $isMatchingElement = function (\DOMNode $context, string $selector) use ($simpleSelectors) {
            foreach ($simpleSelectors as $simpleSelector => $callback) {
                $match = null;
                if (preg_match('/^' . (str_replace('?:', '', $simpleSelector)) . '$/', $selector, $match) === 1) {
                    return call_user_func($callback, 'validate', [$match], $context);
                }
            }
        };

        $complexSelectors = [];

        $getMatchingElements = function (\DOMNode $context, string $selector, $preferredLimit = null) use (&$simpleSelectors, &$complexSelectors) {

            $processSelector = function (string $mode, string $selector, $operator = null) use (&$processSelector, $simpleSelectors, $complexSelectors, $context, $preferredLimit) {
                $supportedSimpleSelectors = array_keys($simpleSelectors);
                $supportedSimpleSelectorsExpression = '(?:(?:' . implode(')|(?:', $supportedSimpleSelectors) . '))';
                $supportedSelectors = $supportedSimpleSelectors;
                $supportedComplexOperators = array_keys($complexSelectors);
                if ($operator === null) {
                    $operator = ',';
                    foreach ($supportedComplexOperators as $complexOperator) {
                        array_unshift($supportedSelectors, '(?:(?:(?:' . $supportedSimpleSelectorsExpression . '\s*\\' . $complexOperator . '\s*))+' . $supportedSimpleSelectorsExpression . ')');
                    }
                }
                $supportedSelectorsExpression = '(?:(?:' . implode(')|(?:', $supportedSelectors) . '))';

                $vallidationExpression = '/^(?:(?:' . $supportedSelectorsExpression . '\s*\\' . $operator . '\s*))*' . $supportedSelectorsExpression . '$/';
                if (preg_match($vallidationExpression, $selector) !== 1) {
                    return false;
                }
                $selector .= $operator; // append the seprator at the back for easier matching below

                $result = [];
                if ($mode === 'execute') {
                    $add = function ($element) use ($preferredLimit, &$result) {
                        $found = false;
                        foreach ($result as $addedElement) {
                            if ($addedElement === $element) {
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            $result[] = $element;
                            if ($preferredLimit !== null && sizeof($result) >= $preferredLimit) {
                                return true;
                            }
                        }
                        return false;
                    };
                }

                $selectorsToCall = [];
                $addSelectorToCall = function ($type, $selector, $argument) use (&$selectorsToCall) {
                    $previousIndex = sizeof($selectorsToCall) - 1;
                    // todo optimize complex too
                    if ($type === 1 && isset($selectorsToCall[$previousIndex]) && $selectorsToCall[$previousIndex][0] === $type && $selectorsToCall[$previousIndex][1] === $selector) {
                        $selectorsToCall[$previousIndex][2][] = $argument;
                    } else {
                        $selectorsToCall[] = [$type, $selector, [$argument]];
                    }
                };
                for ($i = 0; $i < 100000; $i++) {
                    $matches = null;
                    preg_match('/^(?<subselector>' . $supportedSelectorsExpression . ')\s*\\' . $operator . '\s*/', $selector, $matches); // getting the next subselector
                    if (isset($matches['subselector'])) {
                        $subSelector = $matches['subselector'];
                        $selectorFound = false;
                        foreach ($simpleSelectors as $simpleSelector => $callback) {
                            $match = null;
                            if (preg_match('/^' . (str_replace('?:', '', $simpleSelector)) . '$/', $subSelector, $match) === 1) { // if simple selector
                                if ($mode === 'parse') {
                                    $result[] = $match[0];
                                } else {
                                    $addSelectorToCall(1, $simpleSelector, $match);
                                    //call_user_func($callback, 'execute', $match, $context, $add);
                                }
                                $selectorFound = true;
                                break;
                            }
                        }
                        if (!$selectorFound) {
                            foreach ($complexSelectors as $complexOperator => $callback) {
                                $subSelectorParts = $processSelector('parse', $subSelector, $complexOperator);
                                if ($subSelectorParts !== false) {
                                    $addSelectorToCall(2, $complexOperator, $subSelectorParts);
                                    //call_user_func($callback, $subSelectorParts, $context, $add);
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
                foreach ($selectorsToCall as $selectorToCall) {
                    if ($selectorToCall[0] === 1) { // is simple selector
                        call_user_func($simpleSelectors[$selectorToCall[1]], 'execute', $selectorToCall[2], $context, $add);
                    } else { // is complex selector
                        call_user_func($complexSelectors[$selectorToCall[1]], $selectorToCall[2][0], $context, $add); // todo optimize and send all arguments
                    }
                }
                return $result;
            };

            return $processSelector('execute', $selector);
        };

        // div p (space between) - all <p> elements inside <div> elements
        $complexSelectors[' '] = function (array $parts, \DOMNode $context, callable $add = null) use (&$getMatchingElements) {
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
        $complexSelectors['>'] = function (array $parts, \DOMNode $context, callable $add = null) use (&$getMatchingElements, &$isMatchingElement) {
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
        $complexSelectors['+'] = function (array $parts, \DOMNode $context, callable $add = null) use (&$getMatchingElements, &$isMatchingElement) {
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
        $complexSelectors['~'] = function (array $parts, \DOMNode $context, callable $add = null) use (&$getMatchingElements, &$isMatchingElement) {
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
