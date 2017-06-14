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
class HTML5DOMDocument extends \DOMDocument
{

    use \IvoPetkov\HTML5DOMDocument\Internal\QuerySelectors;

    /**
     * Used to store information about the results of saveHTML. If those results are passed to loadHTML some optimizations are applied.
     * 
     * @var array
     */
    static private $savedHTML = [];

    /**
     *
     * @var array 
     */
    static private $newObjectsCache = [];

    /**
     * Indicates whether an HTML code is loaded
     * 
     * @var boolean
     */
    private $loaded = false;

    /**
     * Creates a new \IvoPetkov\HTML5DOMDocument object
     * 
     * @param string $version The version number of the document as part of the XML declaration.
     * @param string $encoding The encoding of the document as part of the XML declaration.
     */
    public function __construct($version = null, $encoding = null)
    {
        parent::__construct($version, $encoding);
        $this->registerNodeClass('DOMElement', '\IvoPetkov\HTML5DOMElement');
    }

    /**
     * Load HTML from a string and adds missing doctype, html and body tags
     * 
     * @param string $source The HTML code
     * @param int $options Additional Libxml parameters
     * @return boolean TRUE on success or FALSE on failure
     */
    public function loadHTML($source, $options = 0)
    {
        $isSavedHTML = isset(self::$savedHTML[md5($source)]);
        // Enables libxml errors handling
        $internalErrorsOptionValue = libxml_use_internal_errors();
        if ($internalErrorsOptionValue === false) {
            libxml_use_internal_errors(true);
        }

        $source = trim($source);

        // Add body tag if missing
        if ($source !== '' && preg_match('/\<!DOCTYPE.*?\>/', $source) === 0 && preg_match('/\<html.*?\>/', $source) === 0 && preg_match('/\<body.*?\>/', $source) === 0 && preg_match('/\<head.*?\>/', $source) === 0) {
            $source = '<body>' . $source . '</body>';
        }

        if (strtoupper(substr($source, 0, 9)) !== '<!DOCTYPE') {
            $source = '<!DOCTYPE html>' . $source;
        }

        // Adds temporary head tag
        $charsetTag = '<meta data-html5-dom-document-internal-attribute="charset-meta" http-equiv="content-type" content="text/html; charset=utf-8" />';
        $matches = [];
        preg_match('/\<head.*?\>/', $source, $matches);
        $removeHeadTag = true;
        $removeHtmlTag = true;
        if (isset($matches[0])) { // has head tag
            $removeHeadTag = false;
            $removeHtmlTag = false;
            $insertPosition = strpos($source, $matches[0]) + strlen($matches[0]);
            $source = substr($source, 0, $insertPosition) . $charsetTag . substr($source, $insertPosition);
        } else {
            $matches = [];
            preg_match('/\<html.*?\>/', $source, $matches);
            if (isset($matches[0])) { // has html tag
                $removeHtmlTag = false;
                $source = str_replace($matches[0], $matches[0] . $charsetTag, $source);
            } else {
                $insertPosition = strpos($source, '>') + 1;
                $source = substr($source, 0, $insertPosition) . $charsetTag . substr($source, $insertPosition);
            }
        }

        // Preserve html entities
        $source = preg_replace('/&([a-zA-Z]*);/', 'html5-dom-document-internal-entity1-$1-end', $source);
        $source = preg_replace('/&#([0-9]*);/', 'html5-dom-document-internal-entity2-$1-end', $source);

        $result = parent::loadHTML('<?xml encoding="utf-8" ?>' . $source, is_array($options) ? 0 : $options); //todo dont use array
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
        $metaTagElement = $this->getElementsByTagName('meta')->item(0);
        if ($metaTagElement !== null) {
            if ($metaTagElement->getAttribute('data-html5-dom-document-internal-attribute') === 'charset-meta') {
                $headElement = $metaTagElement->parentNode;
                $htmlElement = $headElement->parentNode;
                $metaTagElement->parentNode->removeChild($metaTagElement);
                if ($removeHeadTag && $headElement->firstChild === null) {
                    $headElement->parentNode->removeChild($headElement);
                }
                if ($removeHtmlTag && $htmlElement->firstChild === null) {
                    $htmlElement->parentNode->removeChild($htmlElement);
                }
            }
        }

        if (!$isSavedHTML) {
            // Update dom if there are multiple head tags
            $headElements = $this->getElementsByTagName('head');
            if ($headElements->length > 1) {
                $firstHeadElement = $headElements->item(0);
                while ($headElements->length > 1) {
                    $nextHeadElement = $headElements->item(1);
                    $nextHeadElementChildren = $nextHeadElement->childNodes;
                    $nextHeadElementChildrenCount = $nextHeadElementChildren->length;
                    for ($i = 0; $i < $nextHeadElementChildrenCount; $i++) {
                        $firstHeadElement->appendChild($nextHeadElementChildren->item(0));
                    }
                    $nextHeadElement->parentNode->removeChild($nextHeadElement);
                }
            }

            // Update dom if there are multiple body tags
            $bodyElements = $this->getElementsByTagName('body');
            if ($bodyElements->length > 1) {
                $firstBodyElement = $bodyElements->item(0);
                while ($bodyElements->length > 1) {
                    $nextBodyElement = $bodyElements->item(1);
                    $nextBodyElementChildren = $nextBodyElement->childNodes;
                    $nextBodyElementChildrenCount = $nextBodyElementChildren->length;
                    for ($i = 0; $i < $nextBodyElementChildrenCount; $i++) {
                        $firstBodyElement->appendChild($nextBodyElementChildren->item(0));
                    }
                    $nextBodyElement->parentNode->removeChild($nextBodyElement);
                }
            }

            if (is_array($options) && isset($options['_internal_disable_duplicates_removal'])) {
                
            } else {
                $this->removeDuplicateTitleTags();
                $this->removeDuplicateMetatags();
                $this->removeElementsWithDuplicateIDs();
            }
        }

        $this->loaded = true;
        return true;
    }

    /**
     * Load HTML from a file and adds missing doctype, html and body tags
     * 
     * @param string $filename The path to the HTML file
     * @param int $options Additional Libxml parameters
     */
    public function loadHTMLFile($filename, $options = 0)
    {
        return $this->loadHTML(file_get_contents($filename), $options);
    }

    /**
     * Adds the HTML tag to the document if missing
     * 
     * @return boolean TRUE on success, FALSE otherwise
     */
    private function addHtmlElementIfMissing()
    {
        if ($this->getElementsByTagName('html')->length === 0) {
            if (!isset(self::$newObjectsCache['htmlelement'])) {
                self::$newObjectsCache['htmlelement'] = new \DOMElement('html');
            }
            $this->appendChild(clone(self::$newObjectsCache['htmlelement']));
            return true;
        }
        return false;
    }

    /**
     * Adds the HEAD tag to the document if missing
     * 
     * @return boolean TRUE on success, FALSE otherwise
     */
    private function addHeadElementIfMissing()
    {
        if ($this->getElementsByTagName('head')->length === 0) {
            $htmlElement = $this->getElementsByTagName('html')->item(0);
            if (!isset(self::$newObjectsCache['headelement'])) {
                self::$newObjectsCache['headelement'] = new \DOMElement('head');
            }
            $headElement = clone(self::$newObjectsCache['headelement']);
            if ($htmlElement->firstChild === null) {
                $htmlElement->appendChild($headElement);
            } else {
                $htmlElement->insertBefore($headElement, $htmlElement->firstChild);
            }
            return true;
        }
        return false;
    }

    /**
     * Adds the BODY tag to the document if missing
     * 
     * @return boolean TRUE on success, FALSE otherwise
     */
    private function addBodyElementIfMissing()
    {
        if ($this->getElementsByTagName('body')->length === 0) {
            if (!isset(self::$newObjectsCache['bodyelement'])) {
                self::$newObjectsCache['bodyelement'] = new \DOMElement('body');
            }
            $this->getElementsByTagName('html')->item(0)->appendChild(clone(self::$newObjectsCache['bodyelement']));
            return true;
        }
        return false;
    }

    /**
     * Dumps the internal document into a string using HTML formatting
     * 
     * @param \DOMNode $node Optional parameter to output a subset of the document.
     * @return string The document (ot node) HTML code as string
     */
    public function saveHTML(\DOMNode $node = NULL)
    {
        if (!$this->loaded) {
            return '<!DOCTYPE html>';
        }
        if ($node !== null) {
            $isInBody = false;
            $isInHead = false;
            $parentNode = $node;
            for ($i = 0; $i < 1000; $i++) {
                $parentNode = $parentNode->parentNode;
                if ($parentNode === null) {
                    break;
                }
                if ($parentNode->nodeName === 'body') {
                    $isInBody = true;
                    break;
                } elseif ($parentNode->nodeName === 'head') {
                    $isInHead = true;
                    break;
                }
            }
            if ($isInBody) {
                $contentElement = $node; // update node children
            } elseif ($isInHead) {
                $contentElement = null; // dont update any children
            } else { // can be body or html element
                $contentElement = $this->getElementsByTagName('body')->item(0); // update body children
            }
        } else {
            $contentElement = $this->getElementsByTagName('body')->item(0); // update body children
        }
        if ($contentElement !== null) { // This preserves the whitespace between the HTML tags
            $contentElements = $contentElement->getElementsByTagName('*');
            $tempNodes = [];
            $tempTextNode = $this->createTextNode('html5-dom-document-internal-content');
            foreach ($contentElements as $element) {
                $newTextNode1 = clone($tempTextNode);
                $parentNode = $element->parentNode;
                $parentNode->insertBefore($newTextNode1, $element);
                if ($element->nextSibling === null) {
                    $newTextNode2 = clone($tempTextNode);
                    $parentNode->appendChild($newTextNode2);
                } else {
                    $newTextNode2 = null;
                }
                $tempNodes[] = [$parentNode, $newTextNode1, $newTextNode2];
            }
        }

        $removeHtmlElement = false;
        $removeHeadElement = false;
        $headElement = $this->getElementsByTagName('head')->item(0);
        if ($headElement === null) {
            if ($this->addHtmlElementIfMissing()) {
                $removeHtmlElement = true;
            }
            if ($this->addHeadElementIfMissing()) {
                $removeHeadElement = true;
            }
            $headElement = $this->getElementsByTagName('head')->item(0);
        }
        $meta = $this->createElement('meta');
        $meta->setAttribute('data-html5-dom-document-internal-attribute', 'charset-meta');
        $meta->setAttribute('http-equiv', 'content-type');
        $meta->setAttribute('content', 'text/html; charset=utf-8');
        $headElement->appendChild($meta);

        $html = parent::saveHTML($node);

        if (isset($tempNodes)) {
            foreach ($tempNodes as $tempNodesData) {
                $tempNodesData[0]->removeChild($tempNodesData[1]);
                if ($tempNodesData[2] !== null) {
                    $tempNodesData[0]->removeChild($tempNodesData[2]);
                }
            }
            unset($tempNodes);
        }

        if ($removeHeadElement) {
            $headElement->parentNode->removeChild($headElement);
        } else {
            $meta->parentNode->removeChild($meta);
        }

        if (strpos($html, 'html5-dom-document-internal-entity') !== false) {
            $html = preg_replace('/html5-dom-document-internal-entity1-(.*?)-end/', '&$1;', $html);
            $html = preg_replace('/html5-dom-document-internal-entity2-(.*?)-end/', '&#$1;', $html);
        }

        $codeToRemove = [
            'html5-dom-document-internal-content',
            '<meta data-html5-dom-document-internal-attribute="charset-meta" http-equiv="content-type" content="text/html; charset=utf-8">',
            '</area>', '</base>', '</br>', '</col>', '</command>', '</embed>', '</hr>', '</img>', '</input>', '</keygen>', '</link>', '</meta>', '</param>', '</source>', '</track>', '</wbr>'
        ];
        if ($removeHeadElement) {
            $codeToRemove[] = '<head></head>';
        }
        if ($removeHtmlElement) {
            $codeToRemove[] = '<html></html>';
        }
        $html = str_replace($codeToRemove, '', $html);

        // Remove the whitespace between the doctype and html tag
        $html = trim(preg_replace('/\>\s\<html/', '><html', $html, 1));
        self::$savedHTML[md5($html)] = 1;
        return $html;
    }

    /**
     * Dumps the internal document into a file using HTML formatting
     * @param string $filename The path to the saved HTML document.
     * @return int the number of bytes written or FALSE if an error occurred
     * @throws \InvalidArgumentException
     */
    public function saveHTMLFile($filename)
    {
        if (!is_string($filename)) {
            throw new \InvalidArgumentException('The filename argument must be of type string');
        }
        if (!is_writable($filename)) {
            return false;
        }
        $result = $this->saveHTML();
        file_put_contents($filename, $result);
        $bytesWritten = filesize($filename);
        if ($bytesWritten === strlen($result)) {
            return $bytesWritten;
        }
        return false;
    }

    /**
     * Returns the first document element matching the selector
     * 
     * @param string $selector CSS query selector
     * @return \DOMElement|null The result DOMElement or null if not found
     */
    public function querySelector($selector)
    {
        return $this->internalQuerySelector($selector);
    }

    /**
     * Returns a list of document elements matching the selector
     * 
     * @param string $selector CSS query selector
     * @return DOMNodeList Returns a list of DOMElements matching the criteria
     * @throws \InvalidArgumentException
     */
    public function querySelectorAll($selector)
    {
        return $this->internalQuerySelectorAll($selector);
    }

    /**
     * Creates an element that will be replaced by the new body in insertHTML
     * 
     * @param string $name The name of the insert target
     * @return \DOMElement A new DOMElement that must be set in the place where the new body will be inserted
     * @throws \InvalidArgumentException
     */
    public function createInsertTarget($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('The name argument must be of type string');
        }
        if (!$this->loaded) {
            $this->loadHTML('');
        }
        $element = $this->createElement('html5-dom-document-insert-target');
        $element->setAttribute('name', $name);
        return $element;
    }

    /**
     * Inserts a HTML document into the current document. The elements from the head and the body will be moved to their proper locations.
     * 
     * @param string $source The HTML code to be inserted
     * @param string $target Body target position. Available values: afterBodyBegin, beforeBodyEnd or insertTarget name.
     * @throws \InvalidArgumentException
     */
    public function insertHTML($source, $target = 'beforeBodyEnd')
    {
        if (!is_string($source)) {
            throw new \InvalidArgumentException('The source argument must be of type string');
        }
        if (!is_string($target)) {
            throw new \InvalidArgumentException('The target argument must be of type string');
        }
        if (!$this->loaded) {
            $this->loadHTML('');
        }

        if (!isset(self::$newObjectsCache['html5domdocument'])) {
            self::$newObjectsCache['html5domdocument'] = new HTML5DOMDocument();
        }
        $domDocument = clone(self::$newObjectsCache['html5domdocument']);
        $domDocument->loadHTML($source, ['_internal_disable_duplicates_removal' => true]);

        $doubleCheckIfNodeExists = function($domDocument, $name, $id) { // getElementById returns and element even if it's removed from the DOM
            $list = $domDocument->getElementsByTagName($name);
            foreach ($list as $node) {
                $nodeID = $node->getAttribute('id');
                if ($nodeID !== '' && $nodeID === $id) {
                    return true;
                }
            }
            return false;
        };

        $currentDomDocument = &$this;
        $getNewChild = function($child) use ($currentDomDocument, $doubleCheckIfNodeExists) {
            if ($child instanceof \DOMElement) {
                $id = $child->getAttribute('id');
                if ($id !== '') {
                    $otherElement = $currentDomDocument->getElementById($id);
                    if ($otherElement !== null) {
                        if ($doubleCheckIfNodeExists($currentDomDocument, $otherElement->nodeName, $id)) {
                            return null;
                        }
                    }
                }
            }
            if ($child->firstChild !== null) {
                $childChildren = $child->getElementsByTagName('*');
                foreach ($childChildren as $childChild) {
                    $id = $childChild->getAttribute('id');
                    if ($id !== '') {
                        $otherElement = $currentDomDocument->getElementById($id);
                        if ($otherElement !== null) {
                            if ($doubleCheckIfNodeExists($currentDomDocument, $otherElement->nodeName, $id)) {
                                $childChild->parentNode->removeChild($childChild);
                            }
                        }
                    }
                }
            }
            return $currentDomDocument->importNode($child, true);
        };

        $copyAttributes = function($sourceNode, $targetNode) {
            foreach ($sourceNode->attributes as $attributeName => $attribute) {
                $targetNode->setAttribute($attributeName, $attribute->value);
            }
        };

        $headTitlesElementsChanged = false;
        $headMetaElementsChanged = false;

        $domDocumentHtmlTags = $domDocument->getElementsByTagName('html');
        if ($domDocumentHtmlTags->length > 0) {
            $htmlElement = $domDocumentHtmlTags->item(0);
            if ($htmlElement->attributes->length > 0) {
                $currentDomHTMLElement = $this->getElementsByTagName('html')->item(0);
                if ($currentDomHTMLElement === null) {
                    $this->addHtmlElementIfMissing();
                    $currentDomHTMLElement = $this->getElementsByTagName('html')->item(0);
                }
                $copyAttributes($htmlElement, $currentDomHTMLElement);
            }
        }

        $domDocumentHeadTags = $domDocument->getElementsByTagName('head');
        if ($domDocumentHeadTags->length > 0) {
            $headElement = $domDocumentHeadTags->item(0);
            $currentDomHeadElement = $this->getElementsByTagName('head')->item(0);
            if ($currentDomHeadElement === null) {
                $this->addHtmlElementIfMissing();
                $this->addHeadElementIfMissing();
                $currentDomHeadElement = $this->getElementsByTagName('head')->item(0);
            }
            $headElementChildren = $headElement->childNodes;
            foreach ($headElementChildren as $headElementChild) {
                $newNode = $getNewChild($headElementChild);
                if ($newNode !== null) {
                    $currentDomHeadElement->appendChild($newNode);
                    $nodeName = $newNode->nodeName;
                    if (!$headTitlesElementsChanged && $nodeName === 'title') {
                        $headTitlesElementsChanged = true;
                    }
                    if (!$headMetaElementsChanged && $nodeName === 'meta') {
                        $headMetaElementsChanged = true;
                    }
                }
            }
            if ($headElement->attributes->length > 0) {
                $copyAttributes($headElement, $currentDomHeadElement);
            }
        }

        $domDocumentBodyTags = $domDocument->getElementsByTagName('body');
        if ($domDocumentBodyTags->length > 0) {
            $bodyElement = $domDocumentBodyTags->item(0);
            $currentDomBodyElement = $this->getElementsByTagName('body')->item(0);
            if ($currentDomBodyElement === null) {
                $this->addHtmlElementIfMissing();
                $this->addBodyElementIfMissing();
                $currentDomBodyElement = $this->getElementsByTagName('body')->item(0);
            }
            $bodyElementChildren = $bodyElement->childNodes;
            if ($bodyElement->attributes->length > 0) {
                $copyAttributes($bodyElement, $currentDomBodyElement);
            }

            if ($target === 'afterBodyBegin') {
                $bodyElementChildrenCount = $bodyElementChildren->length;
                for ($i = $bodyElementChildrenCount - 1; $i >= 0; $i--) {
                    $newNode = $getNewChild($bodyElementChildren->item($i));
                    if ($newNode !== null) {
                        if ($currentDomBodyElement->firstChild === null) {
                            $currentDomBodyElement->appendChild($newNode);
                        } else {
                            $currentDomBodyElement->insertBefore($newNode, $currentDomBodyElement->firstChild);
                        }
                    }
                }
            } else if ($target === 'beforeBodyEnd') {
                foreach ($bodyElementChildren as $bodyElementChild) {
                    $newNode = $getNewChild($bodyElementChild);
                    if ($newNode !== null) {
                        $currentDomBodyElement->appendChild($newNode);
                    }
                }
            } else {
                $targetElements = $this->getElementsByTagName('html5-dom-document-insert-target');
                foreach ($targetElements as $targetElement) {
                    if ($targetElement->getAttribute('name') === $target) {
                        foreach ($bodyElementChildren as $bodyElementChild) {
                            $newNode = $getNewChild($bodyElementChild);
                            if ($newNode !== null) {
                                $targetElement->parentNode->insertBefore($newNode, $targetElement);
                            }
                        }
                    }
                    $targetElement->parentNode->removeChild($targetElement);
                    break;
                }
            }
        }

        if ($headTitlesElementsChanged) {
            $this->removeDuplicateTitleTags();
        }
        if ($headMetaElementsChanged) {
            $this->removeDuplicateMetatags();
        }
    }

    /**
     * The last title element will remain if multiple.
     */
    private function removeDuplicateTitleTags()
    {
        $headElements = $this->getElementsByTagName('head');
        if ($headElements->length > 0) {
            $headElement = $headElements->item(0);
            $titleTags = $headElement->getElementsByTagName('title');
            $titleTagsCount = $titleTags->length;
            for ($i = 0; $i < $titleTagsCount - 1; $i++) {
                $node = $titleTags->item($i);
                $node->parentNode->removeChild($node);
            }
        }
    }

    /**
     * Removes duplicate meta tags. Meta tags checked by name or property attributes.
     */
    private function removeDuplicateMetatags()
    {
        $headElements = $this->getElementsByTagName('head');
        if ($headElements->length > 0) {
            $headElement = $headElements->item(0);
            $metaTags = $headElement->getElementsByTagName('meta');
            if ($metaTags->length > 0) {
                $list = [];
                $idsList = [];
                //for ($i = 0; $i < $metaTags->length; $i++) {
                //    $metaTag = $metaTags->item($i);
                foreach ($metaTags as $metaTag) {
                    $id = $metaTag->getAttribute('name');
                    if ($id !== '') {
                        $id = 'name:' . $id;
                    } else {
                        $id = $metaTag->getAttribute('property');
                        if ($id !== '') {
                            $id = 'property:' . $id;
                        } else {
                            $id = $metaTag->getAttribute('charset');
                            if ($id !== '') {
                                $id = 'charset';
                            }
                        }
                    }
                    if (!isset($idsList[$id])) {
                        $idsList[$id] = 0;
                    }
                    $idsList[$id] ++;
                    $list[] = [$metaTag, $id];
                }
                foreach ($idsList as $id => $count) {
                    if ($count > 1 && $id !== '') {
                        foreach ($list as $i => $item) {
                            if ($item[1] === $id) {
                                $node = $item[0];
                                $node->parentNode->removeChild($node);
                                unset($list[$i]);
                                $count--;
                            }
                            if ($count === 1) {
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Only the first element with a specified id will remain if multiple with the same id are set.
     */
    private function removeElementsWithDuplicateIDs()
    {
        $allElements = $this->getElementsByTagName('*');
        $passedIDs = [];
        foreach ($allElements as $element) {
            $id = $element->getAttribute('id');
            if ($id !== '') {
                if (isset($passedIDs[$id])) {
                    $element->parentNode->removeChild($element);
                } else {
                    $passedIDs[$id] = 1;
                }
            }
        }
    }

}
