<?php

/*
 * HTML5 DOMDocument PHP library (extends DOMDocument)
 * https://github.com/ivopetkov/html5-dom-document-php
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov;

use IvoPetkov\HTML5DOMDocument\Internal\QuerySelectors;

/**
 * Represents a live (can be manipulated) representation of a HTML5 document.
 */
class HTML5DOMDocument extends \DOMDocument
{

    use QuerySelectors;

    /**
     * An option passed to loadHTML() and loadHTMLFile() to disable duplicate element IDs exception.
     */
    const ALLOW_DUPLICATE_IDS = 67108864;

    /**
     * A modification (passed to modify()) that removes all but the last title elements.
     */
    const FIX_MULTIPLE_TITLES = 2;

    /**
     * A modification (passed to modify()) that removes all but the last metatags with matching name or property attributes.
     */
    const FIX_DUPLICATE_METATAGS = 4;

    /**
     * A modification (passed to modify()) that merges multiple head elements.
     */
    const FIX_MULTIPLE_HEADS = 8;

    /**
     * A modification (passed to modify()) that merges multiple body elements.
     */
    const FIX_MULTIPLE_BODIES = 16;

    /**
     * A modification (passed to modify()) that moves charset metatag and title elements first.
     */
    const OPTIMIZE_HEAD = 32;

    /**
     *
     * @var array
     */
    static private $newObjectsCache = [];

    /**
     * Indicates whether an HTML code is loaded.
     *
     * @var boolean
     */
    private $loaded = false;

    /**
     * Creates a new HTML5DOMDocument object.
     *
     * @param string $version The version number of the document as part of the XML declaration.
     * @param string $encoding The encoding of the document as part of the XML declaration.
     */
    public function __construct(string $version = '1.0', string $encoding = '')
    {
        parent::__construct($version, $encoding);
        $this->registerNodeClass('DOMElement', '\IvoPetkov\HTML5DOMElement');
    }

    /**
     * Load HTML from a string.
     *
     * @param string $source The HTML code.
     * @param int $options Additional Libxml parameters.
     * @return boolean TRUE on success or FALSE on failure.
     */
    public function loadHTML($source, $options = 0)
    {
        // Enables libxml errors handling
        $internalErrorsOptionValue = libxml_use_internal_errors();
        if ($internalErrorsOptionValue === false) {
            libxml_use_internal_errors(true);
        }

        $source = trim($source);

        // Add CDATA around script tags content
        $matches = null;
        preg_match_all('/<script(.*?)>/', $source, $matches);
        if (isset($matches[0])) {
            $matches[0] = array_unique($matches[0]);
            foreach ($matches[0] as $match) {
                if (substr($match, -2, 1) !== '/') { // check if ends with />
                    $source = str_replace($match, $match . '<![CDATA[-html5-dom-document-internal-cdata', $source); // Add CDATA after the open tag
                }
            }
        }
        $source = str_replace('</script>', '-html5-dom-document-internal-cdata]]></script>', $source); // Add CDATA before the end tag
        $source = str_replace('<![CDATA[-html5-dom-document-internal-cdata-html5-dom-document-internal-cdata]]>', '', $source); // Clean empty script tags
        $matches = null;
        preg_match_all('/\<!\[CDATA\[-html5-dom-document-internal-cdata.*?-html5-dom-document-internal-cdata\]\]>/s', $source, $matches);
        if (isset($matches[0])) {
            $matches[0] = array_unique($matches[0]);
            foreach ($matches[0] as $match) {
                if (strpos($match, '</') !== false) { // check if contains </
                    $source = str_replace($match, str_replace('</', '<-html5-dom-document-internal-cdata-endtagfix/', $match), $source);
                }
            }
        }

        $autoAddHtmlAndBodyTags = !defined('LIBXML_HTML_NOIMPLIED') || ($options & LIBXML_HTML_NOIMPLIED) === 0;
        $autoAddDoctype = !defined('LIBXML_HTML_NODEFDTD') || ($options & LIBXML_HTML_NODEFDTD) === 0;

        $allowDuplicateIDs = ($options & self::ALLOW_DUPLICATE_IDS) !== 0;

        // Add body tag if missing
        if ($autoAddHtmlAndBodyTags && $source !== '' && preg_match('/\<!DOCTYPE.*?\>/', $source) === 0 && preg_match('/\<html.*?\>/', $source) === 0 && preg_match('/\<body.*?\>/', $source) === 0 && preg_match('/\<head.*?\>/', $source) === 0) {
            $source = '<body>' . $source . '</body>';
        }

        // Add DOCTYPE if missing
        if ($autoAddDoctype && strtoupper(substr($source, 0, 9)) !== '<!DOCTYPE') {
            $source = "<!DOCTYPE html>\n" . $source;
        }

        // Adds temporary head tag
        $charsetTag = '<meta data-html5-dom-document-internal-attribute="charset-meta" http-equiv="content-type" content="text/html; charset=utf-8" />';
        $matches = [];
        preg_match('/\<head.*?\>/', $source, $matches);
        $removeHeadTag = false;
        $removeHtmlTag = false;
        if (isset($matches[0])) { // has head tag
            $insertPosition = strpos($source, $matches[0]) + strlen($matches[0]);
            $source = substr($source, 0, $insertPosition) . $charsetTag . substr($source, $insertPosition);
        } else {
            $matches = [];
            preg_match('/\<html.*?\>/', $source, $matches);
            if (isset($matches[0])) { // has html tag
                $source = str_replace($matches[0], $matches[0] . '<head>' . $charsetTag . '</head>', $source);
            } else {
                $source = '<head>' . $charsetTag . '</head>' . $source;
                $removeHtmlTag = true;
            }
            $removeHeadTag = true;
        }

        // Preserve html entities
        $source = preg_replace('/&([a-zA-Z]*);/', 'html5-dom-document-internal-entity1-$1-end', $source);
        $source = preg_replace('/&#([0-9]*);/', 'html5-dom-document-internal-entity2-$1-end', $source);

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
        $metaTagElement = $this->getElementsByTagName('meta')->item(0);
        if ($metaTagElement !== null) {
            if ($metaTagElement->getAttribute('data-html5-dom-document-internal-attribute') === 'charset-meta') {
                $headElement = $metaTagElement->parentNode;
                $htmlElement = $headElement->parentNode;
                $metaTagElement->parentNode->removeChild($metaTagElement);
                if ($removeHeadTag && $headElement !== null && $headElement->parentNode !== null && ($headElement->firstChild === null || ($headElement->childNodes->length === 1 && $headElement->firstChild instanceof \DOMText))) {
                    $headElement->parentNode->removeChild($headElement);
                }
                if ($removeHtmlTag && $htmlElement !== null && $htmlElement->parentNode !== null && $htmlElement->firstChild === null) {
                    $htmlElement->parentNode->removeChild($htmlElement);
                }
            }
        }

        if (!$allowDuplicateIDs) {
            $matches = [];
            preg_match_all('/\sid[\s]*=[\s]*(["\'])(.*?)\1/', $source, $matches);
            if (!empty($matches[2]) && max(array_count_values($matches[2])) > 1) {
                $elementIDs = [];
                $walkChildren = function ($element) use (&$walkChildren, &$elementIDs) {
                    foreach ($element->childNodes as $child) {
                        if ($child instanceof \DOMElement) {
                            if ($child->attributes->length > 0) { // Performance optimization
                                $id = $child->getAttribute('id');
                                if ($id !== '') {
                                    if (isset($elementIDs[$id])) {
                                        throw new \Exception('A DOM node with an ID value "' . $id . '" already exists! Pass the HTML5DOMDocument::ALLOW_DUPLICATE_IDS option to disable this check.');
                                    } else {
                                        $elementIDs[$id] = true;
                                    }
                                }
                            }
                            $walkChildren($child);
                        }
                    }
                };
                $walkChildren($this);
            }
        }

        $this->loaded = true;
        return true;
    }

    /**
     * Load HTML from a file.
     *
     * @param string $filename The path to the HTML file.
     * @param int $options Additional Libxml parameters.
     */
    public function loadHTMLFile($filename, $options = 0)
    {
        return $this->loadHTML(file_get_contents($filename), $options);
    }

    /**
     * Adds the HTML tag to the document if missing.
     *
     * @return boolean TRUE on success, FALSE otherwise.
     */
    private function addHtmlElementIfMissing(): bool
    {
        if ($this->getElementsByTagName('html')->length === 0) {
            if (!isset(self::$newObjectsCache['htmlelement'])) {
                self::$newObjectsCache['htmlelement'] = new \DOMElement('html');
            }
            $this->appendChild(clone (self::$newObjectsCache['htmlelement']));
            return true;
        }
        return false;
    }

    /**
     * Adds the HEAD tag to the document if missing.
     *
     * @return boolean TRUE on success, FALSE otherwise.
     */
    private function addHeadElementIfMissing(): bool
    {
        if ($this->getElementsByTagName('head')->length === 0) {
            $htmlElement = $this->getElementsByTagName('html')->item(0);
            if (!isset(self::$newObjectsCache['headelement'])) {
                self::$newObjectsCache['headelement'] = new \DOMElement('head');
            }
            $headElement = clone (self::$newObjectsCache['headelement']);
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
     * Adds the BODY tag to the document if missing.
     *
     * @return boolean TRUE on success, FALSE otherwise.
     */
    private function addBodyElementIfMissing(): bool
    {
        if ($this->getElementsByTagName('body')->length === 0) {
            if (!isset(self::$newObjectsCache['bodyelement'])) {
                self::$newObjectsCache['bodyelement'] = new \DOMElement('body');
            }
            $this->getElementsByTagName('html')->item(0)->appendChild(clone (self::$newObjectsCache['bodyelement']));
            return true;
        }
        return false;
    }

    /**
     * Dumps the internal document into a string using HTML formatting.
     *
     * @param \DOMNode $node Optional parameter to output a subset of the document.
     * @return string The document (or node) HTML code as string.
     */
    public function saveHTML(\DOMNode $node = null): string
    {
        $nodeMode = $node !== null;
        if ($nodeMode && $node instanceof \DOMDocument) {
            $nodeMode = false;
        }

        if ($nodeMode) {
            if (!isset(self::$newObjectsCache['html5domdocument'])) {
                self::$newObjectsCache['html5domdocument'] = new HTML5DOMDocument();
            }
            $tempDomDocument = clone (self::$newObjectsCache['html5domdocument']);
            if ($node->nodeName === 'html') {
                $tempDomDocument->loadHTML('<!DOCTYPE html>');
                $tempDomDocument->appendChild($tempDomDocument->importNode(clone ($node), true));
                $html = $tempDomDocument->saveHTML();
                $html = substr($html, 16); // remove the DOCTYPE + the new line after
            } elseif ($node->nodeName === 'head' || $node->nodeName === 'body') {
                $tempDomDocument->loadHTML("<!DOCTYPE html>\n<html></html>");
                $tempDomDocument->childNodes[1]->appendChild($tempDomDocument->importNode(clone ($node), true));
                $html = $tempDomDocument->saveHTML();
                $html = substr($html, 22, -7); // remove the DOCTYPE + the new line after + html tag
            } else {
                $isInHead = false;
                $parentNode = $node;
                for ($i = 0; $i < 1000; $i++) {
                    $parentNode = $parentNode->parentNode;
                    if ($parentNode === null) {
                        break;
                    }
                    if ($parentNode->nodeName === 'body') {
                        break;
                    } elseif ($parentNode->nodeName === 'head') {
                        $isInHead = true;
                        break;
                    }
                }
                $tempDomDocument->loadHTML("<!DOCTYPE html>\n<html>" . ($isInHead ? '<head></head>' : '<body></body>') . '</html>');
                $tempDomDocument->childNodes[1]->childNodes[0]->appendChild($tempDomDocument->importNode(clone ($node), true));
                $html = $tempDomDocument->saveHTML();
                $html = substr($html, 28, -14); // remove the DOCTYPE + the new line + html + body or head tags
            }
            $html = trim($html);
        } else {
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
            if ($headElement->firstChild !== null) {
                $headElement->insertBefore($meta, $headElement->firstChild);
            } else {
                $headElement->appendChild($meta);
            }
            $html = parent::saveHTML();
            $html = rtrim($html, "\n");

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
                '</area>', '</base>', '</br>', '</col>', '</command>', '</embed>', '</hr>', '</img>', '</input>', '</keygen>', '</link>', '</meta>', '</param>', '</source>', '</track>', '</wbr>',
                '<![CDATA[-html5-dom-document-internal-cdata', '-html5-dom-document-internal-cdata]]>', '-html5-dom-document-internal-cdata-endtagfix'
            ];
            if ($removeHeadElement) {
                $codeToRemove[] = '<head></head>';
            }
            if ($removeHtmlElement) {
                $codeToRemove[] = '<html></html>';
            }

            $html = str_replace($codeToRemove, '', $html);
        }
        return $html;
    }

    /**
     * Dumps the internal document into a file using HTML formatting.
     * 
     * @param string $filename The path to the saved HTML document.
     * @return int|false the number of bytes written or FALSE if an error occurred.
     */
    #[\ReturnTypeWillChange] // Return type "int|false" is invalid in older supported versions.
    public function saveHTMLFile($filename)
    {
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
     * Returns the first document element matching the selector.
     *
     * @param string $selector A CSS query selector. Available values: *, tagname, tagname#id, #id, tagname.classname, .classname, tagname.classname.classname2, .classname.classname2, tagname[attribute-selector], [attribute-selector], "div, p", div p, div > p, div + p and p ~ ul.
     * @return HTML5DOMElement|null The result DOMElement or null if not found.
     * @throws \InvalidArgumentException
     */
    public function querySelector(string $selector)
    {
        return $this->internalQuerySelector($selector);
    }

    /**
     * Returns a list of document elements matching the selector.
     *
     * @param string $selector A CSS query selector. Available values: *, tagname, tagname#id, #id, tagname.classname, .classname, tagname.classname.classname2, .classname.classname2, tagname[attribute-selector], [attribute-selector], "div, p", div p, div > p, div + p and p ~ ul.
     * @return HTML5DOMNodeList Returns a list of DOMElements matching the criteria.
     * @throws \InvalidArgumentException
     */
    public function querySelectorAll(string $selector)
    {
        return $this->internalQuerySelectorAll($selector);
    }

    /**
     * Creates an element that will be replaced by the new body in insertHTML.
     *
     * @param string $name The name of the insert target.
     * @return HTML5DOMElement A new DOMElement that must be set in the place where the new body will be inserted.
     */
    public function createInsertTarget(string $name)
    {
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
     * @param string $source The HTML code to be inserted.
     * @param string $target Body target position. Available values: afterBodyBegin, beforeBodyEnd or insertTarget name.
     */
    public function insertHTML(string $source, string $target = 'beforeBodyEnd')
    {
        $this->insertHTMLMulti([['source' => $source, 'target' => $target]]);
    }

    /**
     * Inserts multiple HTML documents into the current document. The elements from the head and the body will be moved to their proper locations.
     *
     * @param array $sources An array containing the source of the document to be inserted in the following format: [ ['source'=>'', 'target'=>''], ['source'=>'', 'target'=>''], ... ]
     * @throws \Exception
     */
    public function insertHTMLMulti(array $sources)
    {
        if (!$this->loaded) {
            $this->loadHTML('');
        }

        if (!isset(self::$newObjectsCache['html5domdocument'])) {
            self::$newObjectsCache['html5domdocument'] = new HTML5DOMDocument();
        }

        $currentDomDocument = &$this;

        $copyAttributes = function ($sourceNode, $targetNode) {
            foreach ($sourceNode->attributes as $attributeName => $attribute) {
                $targetNode->setAttribute($attributeName, $attribute->value);
            }
        };

        $currentDomHTMLElement = null;
        $currentDomHeadElement = null;
        $currentDomBodyElement = null;

        $insertTargetsList = null;
        $prepareInsertTargetsList = function () use (&$insertTargetsList) {
            if ($insertTargetsList === null) {
                $insertTargetsList = [];
                $targetElements = $this->getElementsByTagName('html5-dom-document-insert-target');
                foreach ($targetElements as $targetElement) {
                    $insertTargetsList[$targetElement->getAttribute('name')] = $targetElement;
                }
            }
        };

        foreach ($sources as $sourceData) {
            if (!isset($sourceData['source'])) {
                throw new \Exception('Missing source key');
            }
            $source = $sourceData['source'];
            $target = isset($sourceData['target']) ? $sourceData['target'] : 'beforeBodyEnd';

            $domDocument = clone (self::$newObjectsCache['html5domdocument']);
            $domDocument->loadHTML($source, self::ALLOW_DUPLICATE_IDS);

            $htmlElement = $domDocument->getElementsByTagName('html')->item(0);
            if ($htmlElement !== null) {
                if ($htmlElement->attributes->length > 0) {
                    if ($currentDomHTMLElement === null) {
                        $currentDomHTMLElement = $this->getElementsByTagName('html')->item(0);
                        if ($currentDomHTMLElement === null) {
                            $this->addHtmlElementIfMissing();
                            $currentDomHTMLElement = $this->getElementsByTagName('html')->item(0);
                        }
                    }
                    $copyAttributes($htmlElement, $currentDomHTMLElement);
                }
            }

            $headElement = $domDocument->getElementsByTagName('head')->item(0);
            if ($headElement !== null) {
                if ($currentDomHeadElement === null) {
                    $currentDomHeadElement = $this->getElementsByTagName('head')->item(0);
                    if ($currentDomHeadElement === null) {
                        $this->addHtmlElementIfMissing();
                        $this->addHeadElementIfMissing();
                        $currentDomHeadElement = $this->getElementsByTagName('head')->item(0);
                    }
                }
                foreach ($headElement->childNodes as $headElementChild) {
                    $newNode = $currentDomDocument->importNode($headElementChild, true);
                    if ($newNode !== null) {
                        $currentDomHeadElement->appendChild($newNode);
                    }
                }
                if ($headElement->attributes->length > 0) {
                    $copyAttributes($headElement, $currentDomHeadElement);
                }
            }

            $bodyElement = $domDocument->getElementsByTagName('body')->item(0);
            if ($bodyElement !== null) {
                if ($currentDomBodyElement === null) {
                    $currentDomBodyElement = $this->getElementsByTagName('body')->item(0);
                    if ($currentDomBodyElement === null) {
                        $this->addHtmlElementIfMissing();
                        $this->addBodyElementIfMissing();
                        $currentDomBodyElement = $this->getElementsByTagName('body')->item(0);
                    }
                }
                $bodyElementChildren = $bodyElement->childNodes;
                if ($target === 'afterBodyBegin') {
                    $bodyElementChildrenCount = $bodyElementChildren->length;
                    for ($i = $bodyElementChildrenCount - 1; $i >= 0; $i--) {
                        $newNode = $currentDomDocument->importNode($bodyElementChildren->item($i), true);
                        if ($newNode !== null) {
                            if ($currentDomBodyElement->firstChild === null) {
                                $currentDomBodyElement->appendChild($newNode);
                            } else {
                                $currentDomBodyElement->insertBefore($newNode, $currentDomBodyElement->firstChild);
                            }
                        }
                    }
                } elseif ($target === 'beforeBodyEnd') {
                    foreach ($bodyElementChildren as $bodyElementChild) {
                        $newNode = $currentDomDocument->importNode($bodyElementChild, true);
                        if ($newNode !== null) {
                            $currentDomBodyElement->appendChild($newNode);
                        }
                    }
                } else {
                    $prepareInsertTargetsList();
                    if (isset($insertTargetsList[$target])) {
                        $targetElement = $insertTargetsList[$target];
                        $targetElementParent = $targetElement->parentNode;
                        foreach ($bodyElementChildren as $bodyElementChild) {
                            $newNode = $currentDomDocument->importNode($bodyElementChild, true);
                            if ($newNode !== null) {
                                $targetElementParent->insertBefore($newNode, $targetElement);
                            }
                        }
                        $targetElementParent->removeChild($targetElement);
                    }
                }
                if ($bodyElement->attributes->length > 0) {
                    $copyAttributes($bodyElement, $currentDomBodyElement);
                }
            } else { // clear the insert target when there is no body element
                $prepareInsertTargetsList();
                if (isset($insertTargetsList[$target])) {
                    $targetElement = $insertTargetsList[$target];
                    $targetElement->parentNode->removeChild($targetElement);
                }
            }
        }
    }

    /**
     * Applies the modifications specified to the DOM document.
     * 
     * @param int $modifications The modifications to apply. Available values:
     *  - HTML5DOMDocument::FIX_MULTIPLE_TITLES - removes all but the last title elements.
     *  - HTML5DOMDocument::FIX_DUPLICATE_METATAGS - removes all but the last metatags with matching name or property attributes.
     *  - HTML5DOMDocument::FIX_MULTIPLE_HEADS - merges multiple head elements.
     *  - HTML5DOMDocument::FIX_MULTIPLE_BODIES - merges multiple body elements.
     *  - HTML5DOMDocument::OPTIMIZE_HEAD - moves charset metatag and title elements first.
     */
    public function modify($modifications = 0)
    {

        $fixMultipleTitles = ($modifications & self::FIX_MULTIPLE_TITLES) !== 0;
        $fixDuplicateMetatags = ($modifications & self::FIX_DUPLICATE_METATAGS) !== 0;
        $fixMultipleHeads = ($modifications & self::FIX_MULTIPLE_HEADS) !== 0;
        $fixMultipleBodies = ($modifications & self::FIX_MULTIPLE_BODIES) !== 0;
        $optimizeHead = ($modifications & self::OPTIMIZE_HEAD) !== 0;

        $headElements = $this->getElementsByTagName('head');

        if ($fixMultipleHeads) { // Merges multiple head elements.
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
                $headElements = [$firstHeadElement];
            }
        }

        foreach ($headElements as $headElement) {

            if ($fixMultipleTitles) { // Remove all title elements except the last one.
                $titleTags = $headElement->getElementsByTagName('title');
                $titleTagsCount = $titleTags->length;
                for ($i = 0; $i < $titleTagsCount - 1; $i++) {
                    $node = $titleTags->item($i);
                    $node->parentNode->removeChild($node);
                }
            }

            if ($fixDuplicateMetatags) { // Remove all meta tags that has matching name or property attributes.
                $metaTags = $headElement->getElementsByTagName('meta');
                if ($metaTags->length > 0) {
                    $list = [];
                    $idsList = [];
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
                        $idsList[$id]++;
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

            if ($optimizeHead) { // Moves charset metatag and title elements first.
                $titleElement = $headElement->getElementsByTagName('title')->item(0);
                $hasTitleElement = false;
                if ($titleElement !== null && $titleElement->previousSibling !== null) {
                    $headElement->insertBefore($titleElement, $headElement->firstChild);
                    $hasTitleElement = true;
                }
                $metaTags = $headElement->getElementsByTagName('meta');
                $metaTagsLength = $metaTags->length;
                if ($metaTagsLength > 0) {
                    $charsetMetaTag = null;
                    $nodesToMove = [];
                    for ($i = $metaTagsLength - 1; $i >= 0; $i--) {
                        $nodesToMove[$i] = $metaTags->item($i);
                    }
                    for ($i = $metaTagsLength - 1; $i >= 0; $i--) {
                        $nodeToMove = $nodesToMove[$i];
                        if ($charsetMetaTag === null && $nodeToMove->getAttribute('charset') !== '') {
                            $charsetMetaTag = $nodeToMove;
                        }
                        $referenceNode = $headElement->childNodes->item($hasTitleElement ? 1 : 0);
                        if ($nodeToMove !== $referenceNode) {
                            $headElement->insertBefore($nodeToMove, $referenceNode);
                        }
                    }
                    if ($charsetMetaTag !== null && $charsetMetaTag->previousSibling !== null) {
                        $headElement->insertBefore($charsetMetaTag, $headElement->firstChild);
                    }
                }
            }
        }

        if ($fixMultipleBodies) { // Merges multiple body elements.
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
        }
    }
}
