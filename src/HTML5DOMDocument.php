<?php

/*
 * HTML5 DOMDocument PHP library (extends DOMDocument)
 * https://github.com/ivopetkov/html5-dom-document-php
 * Copyright 2016, Ivo Petkov
 * Free to use under the MIT license.
 */

/**
 * 
 */
class HTML5DOMDocument extends DOMDocument
{

    /**
     * 
     * @param string $version
     * @param string $encoding
     */
    function __construct($version = null, $encoding = null)
    {
        parent::__construct($version, $encoding);
        $this->registerNodeClass('DOMElement', 'HTML5DOMElement');
    }

    /**
     * Load HTML from a string and adds missing doctype, html and body tags
     * @param string $source
     * @param int $options
     * @throws Exception
     * @return boolean
     */
    function loadHTML($source, $options = 0)
    {
        $internalErrorsOptionValue = libxml_use_internal_errors();
        if ($internalErrorsOptionValue === false) {
            libxml_use_internal_errors(true);
        }
        $source = trim($source);

        if (stripos($source, '<!DOCTYPE') === false) {
            $source = '<!DOCTYPE html>' . $source;
        }
        $source = str_replace('&nbsp;', 'html5-dom-document-internal-nbsp-prefix<html5-dom-document-internal-nbsp></html5-dom-document-internal-nbsp>html5-dom-document-internal-nbsp-suffix', $source);
        $result = parent::loadHTML('<?xml encoding="utf-8" ?>' . $source, $options);
        if ($internalErrorsOptionValue === false) {
            libxml_use_internal_errors(false);
        }
        if ($result === false) {
            return false;
        }
        $this->encoding = 'utf-8';
        foreach ($this->childNodes as $item) {
            if ($item->nodeType === XML_PI_NODE) {
                $this->removeChild($item);
                break;
            }
        }

        for ($c = 0; $c < 1000; $c++) {
            $nbspElements = $this->getElementsByTagName('html5-dom-document-internal-nbsp');
            $nbspElementsCount = $nbspElements->length;
            if ($nbspElementsCount === 0) {
                break;
            }
            for ($i = 0; $i < $nbspElementsCount; $i++) {
                $nbspElement = $nbspElements->item($i);
                if ($nbspElement !== null) {
                    if ($nbspElement->previousSibling instanceof DOMText) {
                        $nbspElement->parentNode->replaceChild($this->createTextNode(substr($nbspElement->previousSibling->nodeValue, 0, -strlen('html5-dom-document-internal-nbsp-prefix'))), $nbspElement->previousSibling);
                    }
                    if ($nbspElement->nextSibling instanceof DOMText) {
                        $nbspElement->parentNode->replaceChild($this->createTextNode(substr($nbspElement->nextSibling->nodeValue, strlen('html5-dom-document-internal-nbsp-suffix'))), $nbspElement->nextSibling);
                    }
                    $nbspElement->parentNode->replaceChild($this->createEntityReference('nbsp'), $nbspElement);
                }
            }
        }
        return true;
    }

    /**
     * Load HTML from a file and adds missing doctype, html and body tags
     * @param string $filename
     * @param int $options
     */
    function loadHTMLFile($filename, $options = 0)
    {
        return $this->loadHTML(file_get_contents($filename), $options);
    }

    /**
     * Dumps the internal document into a string using HTML formatting
     * @param \DOMNode $node
     * @return string
     */
    function saveHTML(\DOMNode $node = NULL)
    {
        $bodyElement = $this->getElementsByTagName('body')->item(0);
        if ($bodyElement !== null) {
            $bodyElements = $bodyElement->getElementsByTagName('*');
            $bodyElementsCount = $bodyElements->length;
            for ($i = 0; $i < $bodyElementsCount; $i++) {
                $bodyElement = $bodyElements->item($i);
                $bodyElement->parentNode->insertBefore($this->createTextNode('html5-dom-document-internal-content'), $bodyElement);
                if ($bodyElement->nextSibling !== null) {
                    $bodyElement->parentNode->insertBefore($this->createTextNode('html5-dom-document-internal-content'), $bodyElement->nextSibling);
                } else {
                    $bodyElement->parentNode->appendChild($this->createTextNode('html5-dom-document-internal-content'));
                }
            }
        }

        $headElements = $this->getElementsByTagName('head');
        $removeHeadElement = false;
        if ($headElements->length === 0) {
            $headElement = new DOMElement('head');
            $this->getElementsByTagName('html')->item(0)->insertBefore($headElement, $this->getElementsByTagName('body')->item(0));
            $removeHeadElement = true;
        }
        $meta = $this->createElement('meta');
        $meta->setAttribute('data-html5-dom-document-internal-attribute', '1');
        $meta->setAttribute('http-equiv', 'content-type');
        $meta->setAttribute('content', 'text/html; charset=utf-8');
        $this->getElementsByTagName('head')->item(0)->appendChild($meta);

        $html = parent::saveHTML($node);

        if ($bodyElement !== null) {
            for ($i = 0; $i < $bodyElementsCount; $i++) {
                $bodyElement = $bodyElements->item($i);
                $bodyElement->parentNode->removeChild($bodyElement->previousSibling);
                $bodyElement->parentNode->removeChild($bodyElement->nextSibling);
            }
        }

        if ($removeHeadElement) {
            $headElement = $this->getElementsByTagName('head')->item(0);
            $headElement->parentNode->removeChild($headElement);
        } else {
            $meta->parentNode->removeChild($meta);
        }

        $html = str_replace('<meta data-html5-dom-document-internal-attribute="1" http-equiv="content-type" content="text/html; charset=utf-8">', '', $html);
        if ($removeHeadElement) {
            $html = str_replace('<head></head>', '', $html);
        }
        $html = str_replace('html5-dom-document-internal-content', '', $html);
        $html = str_replace("\n<html", '<html', $html);

        $html = str_replace('<html5-dom-document-internal-nbsp></html5-dom-document-internal-nbsp>', '&nbsp;', $html);

        $voidElementsList = ['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'];
        foreach ($voidElementsList as $elementName) {
            $html = str_replace('</' . $elementName . '>', '', $html);
        }
        return trim($html);
    }

    /**
     * Dumps the internal document into a file using HTML formatting
     * @param string $filename
     */
    public function saveHTMLFile($filename)
    {
        file_put_contents($filename, $this->saveHTML());
    }

    /**
     * Returns the first document element matching the selector
     * @param string $selector
     * @return DOMElement|null
     */
    public function querySelector($selector)
    {
        $result = $this->querySelectorAll($selector);
        return $result->item(0);
    }

    /**
     * Returns a list of document elements matching the selector
     * @param string $selector
     * @return DOMNodeList
     * @throws Exception
     */
    public function querySelectorAll($selector)
    {
        if (preg_match('/^[a-z]*$/', $selector) === 1) {
            return $this->getElementsByTagName($selector);
        }
        throw new Exception('');
    }

    /**
     * Creates an element that will be replaces by the new body in insertHTML
     * @param string $name
     * @return DOMElement 
     */
    public function createInsertTarget($name)
    {
        $element = $this->createElement('html5-dom-document-insert-target');
        $element->setAttribute('name', $name);
        return $element;
    }

    /**
     * Inserts a HTML document into the current document. The elements from the head and the body will be moved to their proper locations.
     * @param string $source
     * @param string $target afterBodyBegin or beforeBodyEnd or target name
     */
    public function insertHTML($source, $target = 'beforeBodyEnd')
    {
        $domDocument = new HTML5DOMDocument();
        $domDocument->loadHTML($source);

        $headElement = $domDocument->getElementsByTagName('head')->item(0);
        $currentDomHeadElement = null;
        if ($headElement !== null) {
            $headElementChildren = $headElement->childNodes;
            $headElementChildrenCount = $headElementChildren->length;
            for ($i = 0; $i < $headElementChildrenCount; $i++) {
                if ($currentDomHeadElement === null) {
                    $currentDomHeadElements = $this->getElementsByTagName('head');
                    if ($currentDomHeadElements->length === 0) {
                        $currentDomHeadElement = new DOMElement('head');
                        $this->getElementsByTagName('html')->item(0)->insertBefore($currentDomHeadElement, $this->getElementsByTagName('body')->item(0));
                    }
                    $currentDomHeadElement = $this->getElementsByTagName('head')->item(0);
                }
                $currentDomHeadElement->appendChild($this->importNode($headElementChildren->item($i), true));
            }
        }

        $bodyElement = $domDocument->getElementsByTagName('body')->item(0);
        if ($bodyElement !== null) {
            $currentDomBodyElement = $this->getElementsByTagName('body')->item(0);
            $bodyElementChildren = $bodyElement->childNodes;
            $bodyElementChildrenCount = $bodyElementChildren->length;
            if ($target === 'afterBodyBegin') {
                for ($i = $bodyElementChildrenCount - 1; $i >= 0; $i--) {
                    $newNode = $this->importNode($bodyElementChildren->item($i), true);
                    if ($currentDomBodyElement->firstChild === null) {
                        $currentDomBodyElement->appendChild($newNode);
                    } else {
                        $currentDomBodyElement->insertBefore($newNode, $currentDomBodyElement->firstChild);
                    }
                }
            } else if ($target === 'beforeBodyEnd') {
                for ($i = 0; $i < $bodyElementChildrenCount; $i++) {
                    $newNode = $this->importNode($bodyElementChildren->item($i), true);
                    $currentDomBodyElement->appendChild($newNode);
                }
            } else {
                $targetElements = $this->getElementsByTagName('html5-dom-document-insert-target');
                $targetElementsCount = $targetElements->length;
                for ($j = 0; $j < $targetElementsCount; $j++) {
                    $targetElement = $targetElements->item($j);
                    if ($targetElement->getAttribute('name') === $target) {
                        for ($i = 0; $i < $bodyElementChildrenCount; $i++) {
                            $newNode = $this->importNode($bodyElementChildren->item($i), true);
                            $targetElement->parentNode->insertBefore($newNode, $targetElement);
                        }
                    }
                    $targetElement->parentNode->removeChild($targetElement);
                    break;
                }
            }
        }
    }

}
