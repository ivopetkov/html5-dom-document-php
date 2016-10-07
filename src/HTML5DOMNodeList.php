<?php

/*
 * HTML5 DOMDocument PHP library (extends DOMDocument)
 * https://github.com/ivopetkov/html5-dom-document-php
 * Copyright 2016, Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov;

/**
 * @property-read int $length The list items count
 */
class HTML5DOMNodeList extends \ArrayObject
{

    /**
     * Returns the item at the specified index
     * 
     * @param int $index The item index
     * @return \IvoPetkov\HTML5DOMElement|null The item at the specified index or null if not existent
     */
    public function item($index)
    {
        return $this->offsetExists($index) ? $this->offsetGet($index) : null;
    }

    /**
     * Returns the value for the property specified
     * 
     * @param string $name The name of the property
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if ($name === 'length') {
            return sizeof($this);
        }
        throw new \Exception('Undefined property: \IvoPetkov\HTML5DOMNodeList::$' . $name);
    }

}
