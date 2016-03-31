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
class HTML5DOMElement extends \DOMElement
{

    function __get($name)
    {
        if ($name === 'innerHTML') {
            $html = $this->ownerDocument->saveHTML($this);
            $nodeName = $this->nodeName;
            return preg_replace('@^<' . $nodeName . '[^>]*>|</' . $nodeName . '>$@', '', $html);
        } elseif ($name === 'outerHTML') {
            return $this->ownerDocument->saveHTML($this);
        } else {
            throw new Exception('Undefined property: HTML5DOMElement::$' . $name);
        }
    }

    function getAttributes()
    {
        $attributesCount = $this->attributes->length;
        $attributes = [];
        for ($i = 0; $i < $attributesCount; $i++) {
            $attribute = $this->attributes->item($i);
            $attributes[$attribute->name] = $attribute->value;
        }
        return $attributes;
    }

}
