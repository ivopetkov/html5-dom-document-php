# IvoPetkov\HTML5DOMDocument

Represents a live (can be manipulated) representation of a HTML5 document.

```php
IvoPetkov\HTML5DOMDocument extends DOMDocument implements DOMParentNode {

	/* Constants */
	const int ALLOW_DUPLICATE_IDS
	const int FIX_DUPLICATE_METATAGS
	const int FIX_DUPLICATE_STYLES
	const int FIX_MULTIPLE_BODIES
	const int FIX_MULTIPLE_HEADS
	const int FIX_MULTIPLE_TITLES
	const int OPTIMIZE_HEAD

	/* Methods */
	public __construct ( [ string $version = '1.0' [, string $encoding = '' ]] )
	public HTML5DOMElement createInsertTarget ( string $name )
	public void insertHTML ( string $source [, string $target = 'beforeBodyEnd' ] )
	public void insertHTMLMulti ( array $sources )
	public bool loadHTML ( string $source [, int $options = 0 ] )
	public bool loadHTMLFile ( string $filename [, int $options = 0 ] )
	public void modify ( [ int $modifications = 0 ] )
	public HTML5DOMElement|null querySelector ( string $selector )
	public HTML5DOMNodeList querySelectorAll ( string $selector )
	public string saveHTML ( [ DOMNode $node ] )
	public int|false saveHTMLFile ( string $filename )

}
```

## Extends

##### [DOMDocument](http://php.net/manual/en/class.domdocument.php)

## Implements

##### [DOMParentNode](http://php.net/manual/en/class.domparentnode.php)

## Constants

##### const int ALLOW_DUPLICATE_IDS

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An option passed to loadHTML() and loadHTMLFile() to disable duplicate element IDs exception.

##### const int FIX_DUPLICATE_METATAGS

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A modification (passed to modify()) that removes all but the last metatags with matching name or property attributes.

##### const int FIX_DUPLICATE_STYLES

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A modification (passed to modify()) that removes all but first styles with duplicate content.

##### const int FIX_MULTIPLE_BODIES

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A modification (passed to modify()) that merges multiple body elements.

##### const int FIX_MULTIPLE_HEADS

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A modification (passed to modify()) that merges multiple head elements.

##### const int FIX_MULTIPLE_TITLES

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A modification (passed to modify()) that removes all but the last title elements.

##### const int OPTIMIZE_HEAD

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A modification (passed to modify()) that moves charset metatag and title elements first.

## Properties

### Inherited from [DOMDocument](http://php.net/manual/en/class.domdocument.php)

##### public  $actualEncoding

##### public  $childElementCount

##### public  $config

##### public  $doctype

##### public  $documentElement

##### public  $documentURI

##### public  $encoding

##### public  $firstElementChild

##### public  $formatOutput

##### public  $implementation

##### public  $lastElementChild

##### public  $preserveWhiteSpace

##### public  $recover

##### public  $resolveExternals

##### public  $standalone

##### public  $strictErrorChecking

##### public  $substituteEntities

##### public  $validateOnParse

##### public  $version

##### public  $xmlEncoding

##### public  $xmlStandalone

##### public  $xmlVersion

### Inherited from [DOMNode](http://php.net/manual/en/class.domnode.php)

##### public  $attributes

##### public  $baseURI

##### public  $childNodes

##### public  $firstChild

##### public  $lastChild

##### public  $localName

##### public  $namespaceURI

##### public  $nextSibling

##### public  $nodeName

##### public  $nodeType

##### public  $nodeValue

##### public  $ownerDocument

##### public  $parentNode

##### public  $prefix

##### public  $previousSibling

##### public  $textContent

## Methods

##### public [__construct](ivopetkov.html5domdocument.__construct.method.md) ( [ string $version = '1.0' [, string $encoding = '' ]] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates a new HTML5DOMDocument object.

##### public HTML5DOMElement [createInsertTarget](ivopetkov.html5domdocument.createinserttarget.method.md) ( string $name )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates an element that will be replaced by the new body in insertHTML.

##### public void [insertHTML](ivopetkov.html5domdocument.inserthtml.method.md) ( string $source [, string $target = 'beforeBodyEnd' ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Inserts a HTML document into the current document. The elements from the head and the body will be moved to their proper locations.

##### public void [insertHTMLMulti](ivopetkov.html5domdocument.inserthtmlmulti.method.md) ( array $sources )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Inserts multiple HTML documents into the current document. The elements from the head and the body will be moved to their proper locations.

##### public bool [loadHTML](ivopetkov.html5domdocument.loadhtml.method.md) ( string $source [, int $options = 0 ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Load HTML from a string.

##### public bool [loadHTMLFile](ivopetkov.html5domdocument.loadhtmlfile.method.md) ( string $filename [, int $options = 0 ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Load HTML from a file.

##### public void [modify](ivopetkov.html5domdocument.modify.method.md) ( [ int $modifications = 0 ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Applies the modifications specified to the DOM document.

##### public HTML5DOMElement|null [querySelector](ivopetkov.html5domdocument.queryselector.method.md) ( string $selector )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the first document element matching the selector.

##### public HTML5DOMNodeList [querySelectorAll](ivopetkov.html5domdocument.queryselectorall.method.md) ( string $selector )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns a list of document elements matching the selector.

##### public string [saveHTML](ivopetkov.html5domdocument.savehtml.method.md) ( [ [DOMNode](http://php.net/manual/en/class.domnode.php) $node ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dumps the internal document into a string using HTML formatting.

##### public int|false [saveHTMLFile](ivopetkov.html5domdocument.savehtmlfile.method.md) ( string $filename )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dumps the internal document into a file using HTML formatting.

### Inherited from [DOMDocument](http://php.net/manual/en/class.domdocument.php)

##### public void [adoptNode](http://php.net/manual/en/domdocument.adoptnode.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $node )

##### public void [append](http://php.net/manual/en/domdocument.append.php) ( [  $nodes ] )

##### public void [createAttribute](http://php.net/manual/en/domdocument.createattribute.php) ( string $localName )

##### public void [createAttributeNS](http://php.net/manual/en/domdocument.createattributens.php) ( string|null $namespace , string $qualifiedName )

##### public void [createCDATASection](http://php.net/manual/en/domdocument.createcdatasection.php) ( string $data )

##### public void [createComment](http://php.net/manual/en/domdocument.createcomment.php) ( string $data )

##### public void [createDocumentFragment](http://php.net/manual/en/domdocument.createdocumentfragment.php) ( void )

##### public void [createElement](http://php.net/manual/en/domdocument.createelement.php) ( string $localName [, string $value = '' ] )

##### public void [createElementNS](http://php.net/manual/en/domdocument.createelementns.php) ( string|null $namespace , string $qualifiedName [, string $value = '' ] )

##### public void [createEntityReference](http://php.net/manual/en/domdocument.createentityreference.php) ( string $name )

##### public void [createProcessingInstruction](http://php.net/manual/en/domdocument.createprocessinginstruction.php) ( string $target [, string $data = '' ] )

##### public void [createTextNode](http://php.net/manual/en/domdocument.createtextnode.php) ( string $data )

##### public void [getElementById](http://php.net/manual/en/domdocument.getelementbyid.php) ( string $elementId )

##### public void [getElementsByTagName](http://php.net/manual/en/domdocument.getelementsbytagname.php) ( string $qualifiedName )

##### public void [getElementsByTagNameNS](http://php.net/manual/en/domdocument.getelementsbytagnamens.php) ( string|null $namespace , string $localName )

##### public void [importNode](http://php.net/manual/en/domdocument.importnode.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $node [, bool $deep = false ] )

##### public void [load](http://php.net/manual/en/domdocument.load.php) ( string $filename [, int $options = 0 ] )

##### public void [loadXML](http://php.net/manual/en/domdocument.loadxml.php) ( string $source [, int $options = 0 ] )

##### public void [normalizeDocument](http://php.net/manual/en/domdocument.normalizedocument.php) ( void )

##### public void [prepend](http://php.net/manual/en/domdocument.prepend.php) ( [  $nodes ] )

##### public void [registerNodeClass](http://php.net/manual/en/domdocument.registernodeclass.php) ( string $baseClass , string|null $extendedClass )

##### public void [relaxNGValidate](http://php.net/manual/en/domdocument.relaxngvalidate.php) ( string $filename )

##### public void [relaxNGValidateSource](http://php.net/manual/en/domdocument.relaxngvalidatesource.php) ( string $source )

##### public void [save](http://php.net/manual/en/domdocument.save.php) ( string $filename [, int $options = 0 ] )

##### public void [saveXML](http://php.net/manual/en/domdocument.savexml.php) ( [ [DOMNode](http://php.net/manual/en/class.domnode.php)|null $node [, int $options = 0 ]] )

##### public void [schemaValidate](http://php.net/manual/en/domdocument.schemavalidate.php) ( string $filename [, int $flags = 0 ] )

##### public void [schemaValidateSource](http://php.net/manual/en/domdocument.schemavalidatesource.php) ( string $source [, int $flags = 0 ] )

##### public void [validate](http://php.net/manual/en/domdocument.validate.php) ( void )

##### public void [xinclude](http://php.net/manual/en/domdocument.xinclude.php) ( [ int $options = 0 ] )

### Inherited from [DOMNode](http://php.net/manual/en/class.domnode.php)

##### public void [C14N](http://php.net/manual/en/domnode.c14n.php) ( [ bool $exclusive = false [, bool $withComments = false [, array|null $xpath [, array|null $nsPrefixes ]]]] )

##### public void [C14NFile](http://php.net/manual/en/domnode.c14nfile.php) ( string $uri [, bool $exclusive = false [, bool $withComments = false [, array|null $xpath [, array|null $nsPrefixes ]]]] )

##### public void [appendChild](http://php.net/manual/en/domnode.appendchild.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $node )

##### public void [cloneNode](http://php.net/manual/en/domnode.clonenode.php) ( [ bool $deep = false ] )

##### public void [getLineNo](http://php.net/manual/en/domnode.getlineno.php) ( void )

##### public void [getNodePath](http://php.net/manual/en/domnode.getnodepath.php) ( void )

##### public void [hasAttributes](http://php.net/manual/en/domnode.hasattributes.php) ( void )

##### public void [hasChildNodes](http://php.net/manual/en/domnode.haschildnodes.php) ( void )

##### public void [insertBefore](http://php.net/manual/en/domnode.insertbefore.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $node [, [DOMNode](http://php.net/manual/en/class.domnode.php)|null $child ] )

##### public void [isDefaultNamespace](http://php.net/manual/en/domnode.isdefaultnamespace.php) ( string $namespace )

##### public void [isSameNode](http://php.net/manual/en/domnode.issamenode.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $otherNode )

##### public void [isSupported](http://php.net/manual/en/domnode.issupported.php) ( string $feature , string $version )

##### public void [lookupNamespaceURI](http://php.net/manual/en/domnode.lookupnamespaceuri.php) ( string|null $prefix )

##### public void [lookupPrefix](http://php.net/manual/en/domnode.lookupprefix.php) ( string $namespace )

##### public void [normalize](http://php.net/manual/en/domnode.normalize.php) ( void )

##### public void [removeChild](http://php.net/manual/en/domnode.removechild.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $child )

##### public void [replaceChild](http://php.net/manual/en/domnode.replacechild.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $node , [DOMNode](http://php.net/manual/en/class.domnode.php) $child )

## Details

Location: ~/src/HTML5DOMDocument.php

---

[back to index](index.md)

