# IvoPetkov\HTML5DOMDocument

extends [DOMDocument](http://php.net/manual/en/class.domdocument.php)

Represents a live (can be manipulated) representation of a HTML5 document.

## Methods

##### public [__construct](ivopetkov.html5domdocument.__construct.method.md) ( [ string $version ]  [, string $encoding ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates a new HTML5DOMDocument object.

##### public [DOMElement](http://php.net/manual/en/class.domelement.php) [createInsertTarget](ivopetkov.html5domdocument.createinserttarget.method.md) ( string $name )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates an element that will be replaced by the new body in insertHTML.

##### public void [insertHTML](ivopetkov.html5domdocument.inserthtml.method.md) ( string $source [, string $target = 'beforeBodyEnd' ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Inserts a HTML document into the current document. The elements from the head and the body will be moved to their proper locations.

##### public void [insertHTMLMulti](ivopetkov.html5domdocument.inserthtmlmulti.method.md) ( array $sources )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Inserts multiple HTML documents into the current document. The elements from the head and the body will be moved to their proper locations.

##### public boolean [loadHTML](ivopetkov.html5domdocument.loadhtml.method.md) ( string $source [, int $options = 0 ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Load HTML from a string.

##### public void [loadHTMLFile](ivopetkov.html5domdocument.loadhtmlfile.method.md) ( string $filename [, int $options = 0 ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Load HTML from a file.

##### public [DOMElement](http://php.net/manual/en/class.domelement.php)|null [querySelector](ivopetkov.html5domdocument.queryselector.method.md) ( string $selector )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the first document element matching the selector.

##### public [DOMNodeList](http://php.net/manual/en/class.domnodelist.php) [querySelectorAll](ivopetkov.html5domdocument.queryselectorall.method.md) ( string $selector )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns a list of document elements matching the selector.

##### public string [saveHTML](ivopetkov.html5domdocument.savehtml.method.md) ( [ [DOMNode](http://php.net/manual/en/class.domnode.php) $node ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dumps the internal document into a string using HTML formatting.

##### public int [saveHTMLFile](ivopetkov.html5domdocument.savehtmlfile.method.md) ( string $filename )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dumps the internal document into a file using HTML formatting.

### Inherited from DOMNode:

##### public void [C14N](http://php.net/manual/en/domnode.c14n.php) ( [ NULL $exclusive ]  [, NULL $with_comments ]  [, array $xpath ]  [, array $ns_prefixes ] )

##### public void [C14NFile](http://php.net/manual/en/domnode.c14nfile.php) ( $uri [, NULL $exclusive ]  [, NULL $with_comments ]  [, array $xpath ]  [, array $ns_prefixes ] )

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

### Inherited from DOMDocument:

##### public void [adoptNode](http://php.net/manual/en/domdocument.adoptnode.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $source )

##### public void [createAttribute](http://php.net/manual/en/domdocument.createattribute.php) ( $name )

##### public void [createAttributeNS](http://php.net/manual/en/domdocument.createattributens.php) ( $namespaceURI ,  $qualifiedName )

##### public void [createCDATASection](http://php.net/manual/en/domdocument.createcdatasection.php) ( $data )

##### public void [createComment](http://php.net/manual/en/domdocument.createcomment.php) ( $data )

##### public void [createDocumentFragment](http://php.net/manual/en/domdocument.createdocumentfragment.php) ( void )

##### public void [createElement](http://php.net/manual/en/domdocument.createelement.php) ( $tagName [, NULL $value ] )

##### public void [createElementNS](http://php.net/manual/en/domdocument.createelementns.php) ( $namespaceURI ,  $qualifiedName [, NULL $value ] )

##### public void [createEntityReference](http://php.net/manual/en/domdocument.createentityreference.php) ( $name )

##### public void [createProcessingInstruction](http://php.net/manual/en/domdocument.createprocessinginstruction.php) ( $target ,  $data )

##### public void [createTextNode](http://php.net/manual/en/domdocument.createtextnode.php) ( $data )

##### public void [getElementById](http://php.net/manual/en/domdocument.getelementbyid.php) ( $elementId )

##### public void [getElementsByTagName](http://php.net/manual/en/domdocument.getelementsbytagname.php) ( $tagName )

##### public void [getElementsByTagNameNS](http://php.net/manual/en/domdocument.getelementsbytagnamens.php) ( $namespaceURI ,  $localName )

##### public void [importNode](http://php.net/manual/en/domdocument.importnode.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $importedNode ,  $deep )

##### public void [load](http://php.net/manual/en/domdocument.load.php) ( $source [, NULL $options ] )

##### public void [loadXML](http://php.net/manual/en/domdocument.loadxml.php) ( $source [, NULL $options ] )

##### public void [normalizeDocument](http://php.net/manual/en/domdocument.normalizedocument.php) ( void )

##### public void [registerNodeClass](http://php.net/manual/en/domdocument.registernodeclass.php) ( $baseClass ,  $extendedClass )

##### public void [relaxNGValidate](http://php.net/manual/en/domdocument.relaxngvalidate.php) ( $filename )

##### public void [relaxNGValidateSource](http://php.net/manual/en/domdocument.relaxngvalidatesource.php) ( $source )

##### public void [renameNode](http://php.net/manual/en/domdocument.renamenode.php) ( [DOMNode](http://php.net/manual/en/class.domnode.php) $node ,  $namespaceURI ,  $qualifiedName )

##### public void [save](http://php.net/manual/en/domdocument.save.php) ( $file )

##### public void [saveXML](http://php.net/manual/en/domdocument.savexml.php) ( [ [DOMNode](http://php.net/manual/en/class.domnode.php) $node ]  [, NULL $options ] )

##### public void [schemaValidate](http://php.net/manual/en/domdocument.schemavalidate.php) ( $filename )

##### public void [schemaValidateSource](http://php.net/manual/en/domdocument.schemavalidatesource.php) ( $source )

##### public void [validate](http://php.net/manual/en/domdocument.validate.php) ( void )

##### public void [xinclude](http://php.net/manual/en/domdocument.xinclude.php) ( [ NULL $options ] )

## Details

File: /src/HTML5DOMDocument.php

---

[back to index](index.md)

