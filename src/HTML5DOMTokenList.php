<?php

/*
 * HTML5 DOMDocument PHP library (extends DOMDocument)
 * https://github.com/ivopetkov/html5-dom-document-php
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov;

use ArrayIterator;
use DOMElement;

/**
 * Represents a set of space-separated tokens of an element attribute.
 * 
 * @property-read int $length The number of tokens.
 * @property-read string $value A space-separated list of the tokens.
 */
class HTML5DOMTokenList
{

    /**
     * @var string
     */
    private $attributeName;

    /**
     * @var DOMElement
     */
    private $element;

    /**
     * @var string[]
     */
    private $tokens;

    /**
     * @var string
     */
    private $previousValue;

    /**
     * Creates a list of space-separated tokens based on the attribute value of an element.
     * 
     * @param DOMElement $element The DOM element.
     * @param string $attributeName The name of the attribute.
     */
    public function __construct(DOMElement $element, string $attributeName)
    {
        $this->element = $element;
        $this->attributeName = $attributeName;
        $this->previousValue = null;
        $this->tokenize();
    }

    /**
     * Adds the given tokens to the list.
     * 
     * @param string[] $tokens The tokens you want to add to the list.
     * @return void
     */
    public function add(string ...$tokens)
    {
        if (count($tokens) === 0) {
            return;
        }
        foreach ($tokens as $t) {
            if (in_array($t, $this->tokens)) {
                continue;
            }
            $this->tokens[] = $t;
        }
        $this->setAttributeValue();
    }

    /**
     * Removes the specified tokens from the list. If the string does not exist in the list, no error is thrown.
     * 
     * @param string[] $tokens The token you want to remove from the list.
     * @return void
     */
    public function remove(string ...$tokens)
    {
        if (count($tokens) === 0) {
            return;
        }
        if (count($this->tokens) === 0) {
            return;
        }
        foreach ($tokens as $t) {
            $i = array_search($t, $this->tokens);
            if ($i === false) {
                continue;
            }
            array_splice($this->tokens, $i, 1);
        }
        $this->setAttributeValue();
    }

    /**
     * Returns an item in the list by its index (returns null if the number is greater than or equal to the length of the list).
     * 
     * @param int $index The zero-based index of the item you want to return.
     * @return null|string
     */
    public function item(int $index)
    {
        $this->tokenize();
        if ($index >= count($this->tokens)) {
            return null;
        }
        return $this->tokens[$index];
    }

    /**
     * Removes a given token from the list and returns false. If token doesn't exist it's added and the function returns true.
     * 
     * @param string $token The token you want to toggle.
     * @param bool $force A Boolean that, if included, turns the toggle into a one way-only operation. If set to false, the token will only be removed but not added again. If set to true, the token will only be added but not removed again.
     * @return bool false if the token is not in the list after the call, or true if the token is in the list after the call.
     */
    public function toggle(string $token, bool $force = null): bool
    {
        $this->tokenize();
        $isThereAfter = false;
        $i = array_search($token, $this->tokens);
        if (is_null($force)) {
            if ($i === false) {
                $this->tokens[] = $token;
                $isThereAfter = true;
            } else {
                array_splice($this->tokens, $i, 1);
            }
        } else {
            if ($force) {
                if ($i === false) {
                    $this->tokens[] = $token;
                }
                $isThereAfter = true;
            } else {
                if ($i !== false) {
                    array_splice($this->tokens, $i, 1);
                }
            }
        }
        $this->setAttributeValue();
        return $isThereAfter;
    }

    /**
     * Returns true if the list contains the given token, otherwise false.
     * 
     * @param string $token The token you want to check for the existence of in the list.
     * @return bool true if the list contains the given token, otherwise false.
     */
    public function contains(string $token): bool
    {
        $this->tokenize();
        return in_array($token, $this->tokens);
    }

    /**
     * Replaces an existing token with a new token.
     * 
     * @param string $old The token you want to replace.
     * @param string $new The token you want to replace $old with.
     * @return void
     */
    public function replace(string $old, string $new)
    {
        if ($old === $new) {
            return;
        }
        $this->tokenize();
        $i = array_search($old, $this->tokens);
        if ($i !== false) {
            $j = array_search($new, $this->tokens);
            if ($j === false) {
                $this->tokens[$i] = $new;
            } else {
                array_splice($this->tokens, $i, 1);
            }
            $this->setAttributeValue();
        }
    }

    /**
     * 
     * @return string
     */
    public function __toString(): string
    {
        $this->tokenize();
        return implode(' ', $this->tokens);
    }

    /**
     * Returns an iterator allowing you to go through all tokens contained in the list.
     * 
     * @return ArrayIterator
     */
    public function entries(): ArrayIterator
    {
        $this->tokenize();
        return new ArrayIterator($this->tokens);
    }

    /**
     * Returns the value for the property specified
     *
     * @param string $name The name of the property
     * @return string The value of the property specified
     * @throws \Exception
     */
    public function __get(string $name)
    {
        if ($name === 'length') {
            $this->tokenize();
            return count($this->tokens);
        } elseif ($name === 'value') {
            return $this->__toString();
        }
        throw new \Exception('Undefined property: HTML5DOMTokenList::$' . $name);
    }

    /**
     * 
     * @return void
     */
    private function tokenize()
    {
        $current = $this->element->getAttribute($this->attributeName);
        if ($this->previousValue === $current) {
            return;
        }
        $this->previousValue = $current;
        $tokens = explode(' ', $current);
        $finals = [];
        foreach ($tokens as $token) {
            if ($token === '') {
                continue;
            }
            if (in_array($token, $finals)) {
                continue;
            }
            $finals[] = $token;
        }
        $this->tokens = $finals;
    }

    /**
     * 
     * @return void
     */
    private function setAttributeValue()
    {
        $value = implode(' ', $this->tokens);
        if ($this->previousValue === $value) {
            return;
        }
        $this->previousValue = $value;
        $this->element->setAttribute($this->attributeName, $value);
    }
}
