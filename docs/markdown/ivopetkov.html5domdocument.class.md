# IvoPetkov\HTML5DOMDocument

extends [DOMDocument](http://php.net/manual/en/domdocument.php)

## Methods

##### public boolean [loadHTML](ivopetkov.html5domdocument.loadhtml.method.md) ( string $source [, int $options = 0 ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Load HTML from a string

##### public void [loadHTMLFile](ivopetkov.html5domdocument.loadhtmlfile.method.md) ( string $filename [, int $options = 0 ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Load HTML from a file

##### public string [saveHTML](ivopetkov.html5domdocument.savehtml.method.md) ( [ [DOMNode](http://php.net/manual/en/domnode.php) $node ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dumps the internal document into a string using HTML formatting

##### public int [saveHTMLFile](ivopetkov.html5domdocument.savehtmlfile.method.md) ( string $filename )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dumps the internal document into a file using HTML formatting

##### public [DOMElement](http://php.net/manual/en/domelement.php)|null [querySelector](ivopetkov.html5domdocument.queryselector.method.md) ( string $selector )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns the first document element matching the selector

##### public [DOMNodeList](http://php.net/manual/en/domnodelist.php) [querySelectorAll](ivopetkov.html5domdocument.queryselectorall.method.md) ( string $selector )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns a list of document elements matching the selector

##### public [DOMElement](http://php.net/manual/en/domelement.php) [createInsertTarget](ivopetkov.html5domdocument.createinserttarget.method.md) ( string $name )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Creates an element that will be replaced by the new body in insertHTML

##### public void [insertHTML](ivopetkov.html5domdocument.inserthtml.method.md) ( string $source [, string $target = 'beforeBodyEnd' ] )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Inserts a HTML document into the current document. The elements from the head and the body will be moved to their proper locations.

##### public void [insertHTMLMulti](ivopetkov.html5domdocument.inserthtmlmulti.method.md) ( array $sources )

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Inserts multiple HTML documents into the current document. The elements from the head and the body will be moved to their proper locations.

### Inherited from DOMDocument:

##### public void [createElement](http://php.net/manual/en/domdocument.createelement.php) ( $tagName [, NULL $value ] )

##### public void [createDocumentFragment](http://php.net/manual/en/domdocument.createdocumentfragment.php) ( void )

##### public void [createTextNode](http://php.net/manual/en/domdocument.createtextnode.php) ( $data )

##### public void [createComment](http://php.net/manual/en/domdocument.createcomment.php) ( $data )

##### public void [createCDATASection](http://php.net/manual/en/domdocument.createcdatasection.php) ( $data )

##### public void [createProcessingInstruction](http://php.net/manual/en/domdocument.createprocessinginstruction.php) ( $target ,  $data )

##### public void [createAttribute](http://php.net/manual/en/domdocument.createattribute.php) ( $name )

##### public void [createEntityReference](http://php.net/manual/en/domdocument.createentityreference.php) ( $name )

##### public void [getElementsByTagName](http://php.net/manual/en/domdocument.getelementsbytagname.php) ( $tagName )

##### public void [importNode](http://php.net/manual/en/domdocument.importnode.php) ( $importedNode ,  $deep )

##### public void [createElementNS](http://php.net/manual/en/domdocument.createelementns.php) ( $namespaceURI ,  $qualifiedName [, NULL $value ] )

##### public void [createAttributeNS](http://php.net/manual/en/domdocument.createattributens.php) ( $namespaceURI ,  $qualifiedName )

##### public void [getElementsByTagNameNS](http://php.net/manual/en/domdocument.getelementsbytagnamens.php) ( $namespaceURI ,  $localName )

##### public void [getElementById](http://php.net/manual/en/domdocument.getelementbyid.php) ( $elementId )

##### public void [adoptNode](http://php.net/manual/en/domdocument.adoptnode.php) ( $source )

##### public void [normalizeDocument](http://php.net/manual/en/domdocument.normalizedocument.php) ( void )

##### public void [renameNode](http://php.net/manual/en/domdocument.renamenode.php) ( $node ,  $namespaceURI ,  $qualifiedName )

##### public void [load](http://php.net/manual/en/domdocument.load.php) ( $source [, NULL $options ] )

##### public void [save](http://php.net/manual/en/domdocument.save.php) ( $file )

##### public void [loadXML](http://php.net/manual/en/domdocument.loadxml.php) ( $source [, NULL $options ] )

##### public void [saveXML](http://php.net/manual/en/domdocument.savexml.php) ( [ NULL $node ]  [, NULL $options ] )

##### public void [validate](http://php.net/manual/en/domdocument.validate.php) ( void )

##### public void [xinclude](http://php.net/manual/en/domdocument.xinclude.php) ( [ NULL $options ] )

##### public void [schemaValidate](http://php.net/manual/en/domdocument.schemavalidate.php) ( $filename )

##### public void [schemaValidateSource](http://php.net/manual/en/domdocument.schemavalidatesource.php) ( $source )

##### public void [relaxNGValidate](http://php.net/manual/en/domdocument.relaxngvalidate.php) ( $filename )

##### public void [relaxNGValidateSource](http://php.net/manual/en/domdocument.relaxngvalidatesource.php) ( $source )

##### public void [registerNodeClass](http://php.net/manual/en/domdocument.registernodeclass.php) ( $baseClass ,  $extendedClass )

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

File: /src/HTML5DOMDocument.php

