# IvoPetkov\HTML5DOMElement
## Properties

`public string $innerHTML`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The HTML code inside the element

`public string $outerHTML`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The HTML code for the element including the code inside

`public \IvoPetkov\HTML5DOMTokenList $classList`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A collection of the class attributes of the element

## Methods

```php
public string getAttribute ( string $name )
```

Returns the value for the attribute name specified

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$name`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The attribute name

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

```php
public array getAttributes ( void )
```

Returns an array containing all attributes

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An associative array containing all attributes

```php
public \DOMElement|null querySelector ( string $selector )
```

Returns the first child element matching the selector

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$selector`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CSS query selector

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The result DOMElement or null if not found

```php
public \DOMNodeList querySelectorAll ( string $selector )
```

Returns a list of children elements matching the selector

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$selector`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CSS query selector

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns a list of DOMElements matching the criteria

### Inherited methods

From DOMElement:

```php
public void setAttribute ( $name ,  $value )
public void removeAttribute ( $name )
public void getAttributeNode ( $name )
public void setAttributeNode ( $newAttr )
public void removeAttributeNode ( $oldAttr )
public void getElementsByTagName ( $name )
public void getAttributeNS ( $namespaceURI ,  $localName )
public void setAttributeNS ( $namespaceURI ,  $qualifiedName ,  $value )
public void removeAttributeNS ( $namespaceURI ,  $localName )
public void getAttributeNodeNS ( $namespaceURI ,  $localName )
public void setAttributeNodeNS ( $newAttr )
public void getElementsByTagNameNS ( $namespaceURI ,  $localName )
public void hasAttribute ( $name )
public void hasAttributeNS ( $namespaceURI ,  $localName )
public void setIdAttribute ( $name ,  $isId )
public void setIdAttributeNS ( $namespaceURI ,  $localName ,  $isId )
public void setIdAttributeNode ( $attr ,  $isId )
```

From DOMNode:

```php
public void insertBefore ( $newChild [, NULL $refChild ] )
public void replaceChild ( $newChild ,  $oldChild )
public void removeChild ( $oldChild )
public void appendChild ( $newChild )
public void hasChildNodes ( void )
public void cloneNode ( $deep )
public void normalize ( void )
public void isSupported ( $feature ,  $version )
public void hasAttributes ( void )
public void compareDocumentPosition ( $other )
public void isSameNode ( $other )
public void lookupPrefix ( $namespaceURI )
public void isDefaultNamespace ( $namespaceURI )
public void lookupNamespaceUri ( $prefix )
public void isEqualNode ( $arg )
public void getFeature ( $feature ,  $version )
public void setUserData ( $key ,  $data ,  $handler )
public void getUserData ( $key )
public void getNodePath ( void )
public void getLineNo ( void )
public void C14N ( [ NULL $exclusive ]  [, NULL $with_comments ]  [, NULL $xpath ]  [, NULL $ns_prefixes ] )
public void C14NFile ( $uri [, NULL $exclusive ]  [, NULL $with_comments ]  [, NULL $xpath ]  [, NULL $ns_prefixes ] )
```

