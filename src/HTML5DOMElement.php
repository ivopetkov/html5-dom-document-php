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

    public function __get($name)
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

    private function updateResult($value)
    {
        $matches = [];
        preg_match_all('/html5-dom-document-internal-entity1-(.*?)-end/', $value, $matches);
        foreach ($matches[0] as $i => $match) {
            $value = str_replace($match, html_entity_decode('&' . $matches[1][$i] . ';'), $value);
        }
        $matches = [];
        preg_match_all('/html5-dom-document-internal-entity2-(.*?)-end/', $value, $matches);
        foreach ($matches[0] as $i => $match) {
            $value = str_replace($match, html_entity_decode('&#' . $matches[1][$i] . ';'), $value);
        }
        return $value;
    }

    public function getAttribute($name)
    {
        return $this->updateResult(parent::getAttribute($name));
    }

    public function getAttributes()
    {
        $attributesCount = $this->attributes->length;
        $attributes = [];
        for ($i = 0; $i < $attributesCount; $i++) {
            $attribute = $this->attributes->item($i);
            $attributes[$attribute->name] = $this->updateResult($attribute->value);
        }
        return $attributes;
    }

    public function __toString()
    {
        return $this->outerHTML;
    }

}
