# IvoPetkov\HTML5DOMElement

Represents a live (can be manipulated) representation of an element in a HTML5 document.

```php
IvoPetkov\HTML5DOMElement extends DOMElement {

	/* Properties */
	public IvoPetkov\HTML5DOMTokenList $classList
	public string $innerHTML
	public string $outerHTML

	/* Methods */
	public string getAttribute ( string $name )
	public array getAttributes ( void )
	public DOMElement|null querySelector ( string $selector )
	public DOMNodeList querySelectorAll ( string $selector )

}
```

## Extends

##### [DOMElement](http://php.net/manual/en/class.domelement.php)

## Properties

##### public [IvoPetkov\HTML5DOMTokenList](ivopetkov.html5domtokenlist.class.md) $classList

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A collection of the class attributes of the element.

##### public string $innerHTML

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The HTML code inside the element.

##### public string $outerHTML

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The HTML code for the element including the code inside.

## Methods

##### public string [getAttribute](ivopetkov.html5domelement.getattribute.method.md) ( string $name )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the value for the attribute name specified.

##### public array [getAttributes](ivopetkov.html5domelement.getattributes.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns an array containing all attributes.

##### public [DOMElement](http://php.net/manual/en/class.domelement.php)|null [querySelector](ivopetkov.html5domelement.queryselector.method.md) ( string $selector )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the first child element matching the selector.

##### public [DOMNodeList](http://php.net/manual/en/class.domnodelist.php) [querySelectorAll](ivopetkov.html5domelement.queryselectorall.method.md) ( string $selector )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns a list of children elements matching the selector.

### Inherited from [DOMElement](http://php.net/manual/en/class.domelement.php)

##### public [__construct](http://php.net/manual/en/domelement.construct.php) ( $name [,  $value [,  $uri ]] )

##### public void [getAttributeNS](http://php.net/manual/en/domelement.getattributens.php) ( $namespaceURI ,  $localName )

##### public void [getAttributeNode](http://php.net/manual/en/domelement.getattributenode.php) ( $name )

##### public void [getAttributeNodeNS](http://php.net/manual/en/domelement.getattributenodens.php) ( $namespaceURI ,  $localName )

##### public void [getElementsByTagName](http://php.net/manual/en/domelement.getelementsbytagname.php) ( $name )

##### public void [getElementsByTagNameNS](http://php.net/manual/en/domelement.getelementsbytagnamens.php) ( $namespaceURI ,  $localName )

##### public void [hasAttribute](http://php.net/manual/en/domelement.hasattribute.php) ( $name )

##### public void [hasAttributeNS](http://php.net/manual/en/domelement.hasattributens.php) ( $namespaceURI ,  $localName )

##### public void [removeAttribute](http://php.net/manual/en/domelement.removeattribute.php) ( $name )

##### public void [removeAttributeNS](http://php.net/manual/en/domelement.removeattributens.php) ( $namespaceURI ,  $localName )

##### public void [removeAttributeNode](http://php.net/manual/en/domelement.removeattributenode.php) ( [DOMAttr](http://php.net/manual/en/class.domattr.php) $oldAttr )

##### public void [setAttribute](http://php.net/manual/en/domelement.setattribute.php) ( $name ,  $value )

##### public void [setAttributeNS](http://php.net/manual/en/domelement.setattributens.php) ( $namespaceURI ,  $qualifiedName ,  $value )

##### public void [setAttributeNode](http://php.net/manual/en/domelement.setattributenode.php) ( [DOMAttr](http://php.net/manual/en/class.domattr.php) $newAttr )

##### public void [setAttributeNodeNS](http://php.net/manual/en/domelement.setattributenodens.php) ( [DOMAttr](http://php.net/manual/en/class.domattr.php) $newAttr )

##### public void [setIdAttribute](http://php.net/manual/en/domelement.setidattribute.php) ( $name ,  $isId )

##### public void [setIdAttributeNS](http://php.net/manual/en/domelement.setidattributens.php) ( $namespaceURI ,  $localName ,  $isId )

##### public void [setIdAttributeNode](http://php.net/manual/en/domelement.setidattributenode.php) ( [DOMAttr](http://php.net/manual/en/class.domattr.php) $attr ,  $isId )

### Inherited from [DOMNode](http://php.net/manual/en/class.domnode.php)

##### public void [C14N](http://php.net/manual/en/domnode.c14n.php) ( [  $exclusive [,  $with_comments [, array $xpath [, array $ns_prefixes ]]]] )

##### public void [C14NFile](http://php.net/manual/en/domnode.c14nfile.php) ( $uri [,  $exclusive [,  $with_comments [, array $xpath [, array $ns_prefixes ]]]] )

##### public void [appendChild](http://php.net/manual/en/domnode.appendchild.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $newChild )

##### public void [cloneNode](http://php.net/manual/en/domnode.clonenode.php) ( $deep )

##### public void [compareDocumentPosition](http://php.net/manual/en/domnode.comparedocumentposition.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $other )

##### public void [getFeature](http://php.net/manual/en/domnode.getfeature.php) ( $feature ,  $version )

##### public void [getLineNo](http://php.net/manual/en/domnode.getlineno.php) ( void )

##### public void [getNodePath](http://php.net/manual/en/domnode.getnodepath.php) ( void )

##### public void [getUserData](http://php.net/manual/en/domnode.getuserdata.php) ( $key )

##### public void [hasAttributes](http://php.net/manual/en/domnode.hasattributes.php) ( void )

##### public void [hasChildNodes](http://php.net/manual/en/domnode.haschildnodes.php) ( void )

##### public void [insertBefore](http://php.net/manual/en/domnode.insertbefore.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $newChild [, [DOMNode](http://php.net/manual/en/class.domnode.php) $refChild ] )

##### public void [isDefaultNamespace](http://php.net/manual/en/domnode.isdefaultnamespace.php) ( $namespaceURI )

##### public void [isEqualNode](http://php.net/manual/en/domnode.isequalnode.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $arg )

##### public void [isSameNode](http://php.net/manual/en/domnode.issamenode.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $other )

##### public void [isSupported](http://php.net/manual/en/domnode.issupported.php) ( $feature ,  $version )

##### public void [lookupNamespaceUri](http://php.net/manual/en/domnode.lookupnamespaceuri.php) ( $prefix )

##### public void [lookupPrefix](http://php.net/manual/en/domnode.lookupprefix.php) ( $namespaceURI )

##### public void [normalize](http://php.net/manual/en/domnode.normalize.php) ( void )

##### public void [removeChild](http://php.net/manual/en/domnode.removechild.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $oldChild )

##### public void [replaceChild](http://php.net/manual/en/domnode.replacechild.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $newChild , [DOMNode](http://php.net/manual/en/class.domnode.php) $oldChild )

##### public void [setUserData](http://php.net/manual/en/domnode.setuserdata.php) ( $key ,  $data ,  $handler )

## Details

Location: ~/src/HTML5DOMElement.php

---

[back to index](index.md)

