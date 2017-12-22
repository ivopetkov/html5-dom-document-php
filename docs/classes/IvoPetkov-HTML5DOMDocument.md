# IvoPetkov\HTML5DOMDocument
## Methods

```php
public boolean loadHTML ( string $source [, int $options = 0 ] )
```

Load HTML from a string and adds missing doctype, html and body tags

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$source`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The HTML code

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$options`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Additional Libxml parameters

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TRUE on success or FALSE on failure

```php
public void loadHTMLFile ( string $filename [, int $options = 0 ] )
```

Load HTML from a file and adds missing doctype, html and body tags

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$filename`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The path to the HTML file

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$options`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Additional Libxml parameters

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
public string saveHTML ( [ \DOMNode $node ] )
```

Dumps the internal document into a string using HTML formatting

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$node`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Optional parameter to output a subset of the document.

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The document (or node) HTML code as string

```php
public int saveHTMLFile ( string $filename )
```

Dumps the internal document into a file using HTML formatting

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$filename`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The path to the saved HTML document.

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;the number of bytes written or FALSE if an error occurred

```php
public \DOMElement|null querySelector ( string $selector )
```

Returns the first document element matching the selector

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$selector`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CSS query selector

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The result DOMElement or null if not found

```php
public \DOMNodeList querySelectorAll ( string $selector )
```

Returns a list of document elements matching the selector

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$selector`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CSS query selector

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns a list of DOMElements matching the criteria

```php
public \DOMElement createInsertTarget ( string $name )
```

Creates an element that will be replaced by the new body in insertHTML

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$name`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The name of the insert target

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A new DOMElement that must be set in the place where the new body will be inserted

```php
public void insertHTML ( string $source [, string $target = 'beforeBodyEnd' ] )
```

Inserts a HTML document into the current document. The elements from the head and the body will be moved to their proper locations.

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$source`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The HTML code to be inserted

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$target`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Body target position. Available values: afterBodyBegin, beforeBodyEnd or insertTarget name.

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

```php
public void insertHTMLMulti ( array $sources )
```

Inserts multiple HTML documents into the current document. The elements from the head and the body will be moved to their proper locations.

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$sources`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An array containing the source of the document to be inserted in the following format: [ ['source'=>'', 'target'=>''], ['source'=>'', 'target'=>''], ... ]

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No value is returned.

### Inherited methods

From DOMDocument:

```php
public void createElement ( $tagName [, NULL $value ] )
public void createDocumentFragment ( void )
public void createTextNode ( $data )
public void createComment ( $data )
public void createCDATASection ( $data )
public void createProcessingInstruction ( $target ,  $data )
public void createAttribute ( $name )
public void createEntityReference ( $name )
public void getElementsByTagName ( $tagName )
public void importNode ( $importedNode ,  $deep )
public void createElementNS ( $namespaceURI ,  $qualifiedName [, NULL $value ] )
public void createAttributeNS ( $namespaceURI ,  $qualifiedName )
public void getElementsByTagNameNS ( $namespaceURI ,  $localName )
public void getElementById ( $elementId )
public void adoptNode ( $source )
public void normalizeDocument ( void )
public void renameNode ( $node ,  $namespaceURI ,  $qualifiedName )
public void load ( $source [, NULL $options ] )
public void save ( $file )
public void loadXML ( $source [, NULL $options ] )
public void saveXML ( [ NULL $node ]  [, NULL $options ] )
public void validate ( void )
public void xinclude ( [ NULL $options ] )
public void schemaValidate ( $filename )
public void schemaValidateSource ( $source )
public void relaxNGValidate ( $filename )
public void relaxNGValidateSource ( $source )
public void registerNodeClass ( $baseClass ,  $extendedClass )
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

