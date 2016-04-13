<?php

/*
 * HTML5 DOMDocument PHP library (extends DOMDocument)
 * https://github.com/ivopetkov/html5-dom-document-php
 * Copyright 2016, Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov;

/**
 * 
 */
class HTML5DOMNodeList extends \ArrayObject
{

    /**
     * 
     * @param int $index
     * @return \IvoPetkov\HTML5DOMElement|null
     */
    function item($index)
    {
        return $this->offsetExists($index) ? $this->offsetGet($index) : null;
    }

    /**
     * 
     * @param string $name
     * @return mixed
     */
    function __get($name)
    {
        if ($name === 'length') {
            return sizeof($this);
        } else {
            trigger_error('Undefined property: \IvoPetkov\HTML5DOMNodeList::$' . $name, E_USER_NOTICE);
        }
    }

}
