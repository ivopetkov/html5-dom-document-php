# IvoPetkov\HTML5DOMElement

extends [DOMElement](http://php.net/manual/en/class.domelement.php)

## Properties

##### public string $innerHTML

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The HTML code inside the element

##### public string $outerHTML

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The HTML code for the element including the code inside

##### public [IvoPetkov\HTML5DOMTokenList](ivopetkov.html5domtokenlist.class.md) $classList

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A collection of the class attributes of the element

## Methods

##### public string [getAttribute](ivopetkov.html5domelement.getattribute.method.md) ( string $name )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the value for the attribute name specified

##### public array [getAttributes](ivopetkov.html5domelement.getattributes.method.md) ( void )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns an array containing all attributes

##### public [DOMElement](http://php.net/manual/en/class.domelement.php)|null [querySelector](ivopetkov.html5domelement.queryselector.method.md) ( string $selector )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the first child element matching the selector

##### public [DOMNodeList](http://php.net/manual/en/class.domnodelist.php) [querySelectorAll](ivopetkov.html5domelement.queryselectorall.method.md) ( string $selector )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns a list of children elements matching the selector

### Inherited from DOMElement:

##### public void [setAttribute](http://php.net/manual/en/domelement.setattribute.php) ( $name ,  $value )

##### public void [removeAttribute](http://php.net/manual/en/domelement.removeattribute.php) ( $name )

##### public void [getAttributeNode](http://php.net/manual/en/domelement.getattributenode.php) ( $name )

##### public void [setAttributeNode](http://php.net/manual/en/domelement.setattributenode.php) ( $newAttr )

##### public void [removeAttributeNode](http://php.net/manual/en/domelement.removeattributenode.php) ( $oldAttr )

##### public void [getElementsByTagName](http://php.net/manual/en/domelement.getelementsbytagname.php) ( $name )

##### public void [getAttributeNS](http://php.net/manual/en/domelement.getattributens.php) ( $namespaceURI ,  $localName )

##### public void [setAttributeNS](http://php.net/manual/en/domelement.setattributens.php) ( $namespaceURI ,  $qualifiedName ,  $value )

##### public void [removeAttributeNS](http://php.net/manual/en/domelement.removeattributens.php) ( $namespaceURI ,  $localName )

##### public void [getAttributeNodeNS](http://php.net/manual/en/domelement.getattributenodens.php) ( $namespaceURI ,  $localName )

##### public void [setAttributeNodeNS](http://php.net/manual/en/domelement.setattributenodens.php) ( $newAttr )

##### public void [getElementsByTagNameNS](http://php.net/manual/en/domelement.getelementsbytagnamens.php) ( $namespaceURI ,  $localName )

##### public void [hasAttribute](http://php.net/manual/en/domelement.hasattribute.php) ( $name )

##### public void [hasAttributeNS](http://php.net/manual/en/domelement.hasattributens.php) ( $namespaceURI ,  $localName )

##### public void [setIdAttribute](http://php.net/manual/en/domelement.setidattribute.php) ( $name ,  $isId )

##### public void [setIdAttributeNS](http://php.net/manual/en/domelement.setidattributens.php) ( $namespaceURI ,  $localName ,  $isId )

##### public void [setIdAttributeNode](http://php.net/manual/en/domelement.setidattributenode.php) ( $attr ,  $isId )

### Inherited from DOMNode:

##### public void [insertBefore](http://php.net/manual/en/domnode.insertbefore.php) ( $newChild [, NULL $refChild ] )

##### public void [replaceChild](http://php.net/manual/en/domnode.replacechild.php) ( $newChild ,  $oldChild )

##### public void [removeChild](http://php.net/manual/en/domnode.removechild.php) ( $oldChild )

##### public void [appendChild](http://php.net/manual/en/domnode.appendchild.php) ( $newChild )

##### public void [hasChildNodes](http://php.net/manual/en/domnode.haschildnodes.php) ( void )

##### public void [cloneNode](http://php.net/manual/en/domnode.clonenode.php) ( $deep )

##### public void [normalize](http://php.net/manual/en/domnode.normalize.php) ( void )

##### public void [isSupported](http://php.net/manual/en/domnode.issupported.php) ( $feature ,  $version )

##### public void [hasAttributes](http://php.net/manual/en/domnode.hasattributes.php) ( void )

##### public void [compareDocumentPosition](http://php.net/manual/en/domnode.comparedocumentposition.php) ( $other )

##### public void [isSameNode](http://php.net/manual/en/domnode.issamenode.php) ( $other )

##### public void [lookupPrefix](http://php.net/manual/en/domnode.lookupprefix.php) ( $namespaceURI )

##### public void [isDefaultNamespace](http://php.net/manual/en/domnode.isdefaultnamespace.php) ( $namespaceURI )

##### public void [lookupNamespaceUri](http://php.net/manual/en/domnode.lookupnamespaceuri.php) ( $prefix )

##### public void [isEqualNode](http://php.net/manual/en/domnode.isequalnode.php) ( $arg )

##### public void [getFeature](http://php.net/manual/en/domnode.getfeature.php) ( $feature ,  $version )

##### public void [setUserData](http://php.net/manual/en/domnode.setuserdata.php) ( $key ,  $data ,  $handler )

##### public void [getUserData](http://php.net/manual/en/domnode.getuserdata.php) ( $key )

##### public void [getNodePath](http://php.net/manual/en/domnode.getnodepath.php) ( void )

##### public void [getLineNo](http://php.net/manual/en/domnode.getlineno.php) ( void )

##### public void [C14N](http://php.net/manual/en/domnode.c14n.php) ( [ NULL $exclusive ]  [, NULL $with_comments ]  [, NULL $xpath ]  [, NULL $ns_prefixes ] )

##### public void [C14NFile](http://php.net/manual/en/domnode.c14nfile.php) ( $uri [, NULL $exclusive ]  [, NULL $with_comments ]  [, NULL $xpath ]  [, NULL $ns_prefixes ] )

## Details

File: /src/HTML5DOMElement.php

