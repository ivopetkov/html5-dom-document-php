# HTML5DOMDocument

HTML5DOMDocument extends the native [DOMDocument](http://php.net/manual/en/class.domdocument.php) library. It fixes some bugs and adds some new functionality.

[![Build Status](https://travis-ci.org/ivopetkov/html5-dom-document-php.svg)](https://travis-ci.org/ivopetkov/html5-dom-document-php)
[![Latest Stable Version](https://poser.pugx.org/ivopetkov/html5-dom-document-php/v/stable)](https://packagist.org/packages/ivopetkov/html5-dom-document-php)
[![codecov.io](https://codecov.io/github/ivopetkov/html5-dom-document-php/coverage.svg?branch=master)](https://codecov.io/github/ivopetkov/html5-dom-document-php?branch=master)
[![License](https://poser.pugx.org/ivopetkov/html5-dom-document-php/license)](https://packagist.org/packages/ivopetkov/html5-dom-document-php)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/dafa5722288b409a9d447fa6aabd572b)](https://www.codacy.com/app/ivo_2/html5-dom-document-php)

## Install via Composer

```shell
composer require ivopetkov/html5-dom-document-php
```

## Differences to the native DOMDocument library

- Preserves white spaces
- Preserves html entities
- Preserves void tags
- Allows **inserting HTML code** that moves the correct parts to their proper places (head elements are inserted in the head, body elements in the body)
- Allows **querying the DOM with CSS selectors** (currently avaiable: *, tagname, tagname#id, tagname.classname, #id, .classname)

## Usage

HTML5DOMDocument is really easy to use - just like you should use DOMDocument.
```php
<?php
require 'vendor/autoload.php';

$dom = new IvoPetkov\HTML5DOMDocument();
$dom->loadHTML('<!DOCTYPE html><html><body>Hello</body></html>');
echo $dom->saveHTML();
```

## Documentation

### Classes

This is a list of all the new methods and properties that the library has added to the [DOMDocument](http://php.net/manual/en/class.domdocument.php) and the [DOMElement](http://php.net/manual/en/class.domelement.php) classes.

#### IvoPetkov\HTML5DOMDocument
##### Methods

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
public DOMNodeList querySelectorAll ( string $selector )
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

#### IvoPetkov\HTML5DOMElement
##### Properties

`public string $innerHTML`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The HTML code inside the element

`public string $outerHTML`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The HTML code for the element including the code inside

##### Methods

```php
public array getAttributes ( void )
```

Returns an array containing all attributes

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;An associative array containing all attributes

```php
public string __toString ( void )
```

Returns the element outerHTML

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The element outerHTML

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
public DOMNodeList querySelectorAll ( string $selector )
```

Returns a list of children elements matching the selector

_Parameters_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;`$selector`

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CSS query selector

_Returns_

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Returns a list of DOMElements matching the criteria

## Examples

Querying the document with CSS selectors and getting the innerHTML and the outerHTML of the elements.

```php
$dom = new IvoPetkov\HTML5DOMDocument();
$dom->loadHTML('<!DOCTYPE html><html><body><h1>Hello</h1><div class="content">This is some text</div></body></html>');

echo $dom->querySelector('h1')->innerHTML;
// Hello

echo $dom->querySelector('.content')->outerHTML;
// <div class="content">This is some text</div><
```

Inserting HTML code into other HTML code.

```php
$dom = new IvoPetkov\HTML5DOMDocument();
$dom->loadHTML('
    <!DOCTYPE html>
    <html>
        <head>
            <style>...</style>
        </head>
        <body>
            <h1>Hello</h1>
        </body>
    </html>
');

$dom->insertHTML('
    <html>
        <head>
            <script>...</script>
        </head>
        <body>
            <div>This is some text</div>
        </body>
    </html>
');

echo $dom->saveHTML();
// <!DOCTYPE html>
//     <html>
//         <head>
//             <style>...</style>
//             <script>...</script>
//         </head>
//         <body>
//             <h1>Hello</h1>
//             <div>This is some text</div>
//         </body>
//     </html>
```

## License
HTML5DOMDocument is open-sourced software. It's free to use under the MIT license. See the [license file](https://github.com/ivopetkov/html5-dom-document-php/blob/master/LICENSE) for more information.

## Author
This library is created and maintained by [Ivo Petkov](https://github.com/ivopetkov/) and some [awesome folks](https://github.com/ivopetkov/html5-dom-document-php/graphs/contributors). Feel free to open new issues and contribute or contact me at [@IvoPetkovCom](https://twitter.com/IvoPetkovCom) or [ivopetkov.com](https://ivopetkov.com).
