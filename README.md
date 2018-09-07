# HTML5DOMDocument

HTML5DOMDocument extends the native [DOMDocument](http://php.net/manual/en/class.domdocument.php) library. It fixes some bugs and adds some new functionality.

[![Build Status](https://travis-ci.org/ivopetkov/html5-dom-document-php.svg)](https://travis-ci.org/ivopetkov/html5-dom-document-php)
[![Latest Stable Version](https://poser.pugx.org/ivopetkov/html5-dom-document-php/v/stable)](https://packagist.org/packages/ivopetkov/html5-dom-document-php)
[![codecov.io](https://codecov.io/github/ivopetkov/html5-dom-document-php/coverage.svg?branch=master)](https://codecov.io/github/ivopetkov/html5-dom-document-php?branch=master)
[![License](https://poser.pugx.org/ivopetkov/html5-dom-document-php/license)](https://packagist.org/packages/ivopetkov/html5-dom-document-php)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/dafa5722288b409a9d447fa6aabd572b)](https://www.codacy.com/app/ivo_2/html5-dom-document-php)

## Why use?

- Preserves html entities (DOMDocument does not)
- Preserves void tags (DOMDocument does not)
- Allows **inserting HTML code** that moves the correct parts to their proper places (head elements are inserted in the head, body elements in the body)
- Allows **querying the DOM with CSS selectors** (currently available: *, tagname, tagname#id, #id, tagname.classname, .classname, tagname[attribute-selector] and [attribute-selector])
- Adds support for element->classList.
- Adds support for element->innerHTML.
- Adds support for element->outerHTML.

## Install via Composer

```shell
composer require ivopetkov/html5-dom-document-php:1.*
```

## Documentation

Full [documentation](https://github.com/ivopetkov/html5-dom-document-php/blob/master/docs/markdown/index.md) is available as part of this repository.

## Examples

Use just like you should use DOMDocument:
```php
<?php
require 'vendor/autoload.php';

$dom = new IvoPetkov\HTML5DOMDocument();
$dom->loadHTML('<!DOCTYPE html><html><body>Hello</body></html>');
echo $dom->saveHTML();
```

Query the document with CSS selectors and get the innerHTML and the outerHTML of the elements:

```php
$dom = new IvoPetkov\HTML5DOMDocument();
$dom->loadHTML('<!DOCTYPE html><html><body><h1>Hello</h1><div class="content">This is some text</div></body></html>');

echo $dom->querySelector('h1')->innerHTML;
// Hello

echo $dom->querySelector('.content')->outerHTML;
// <div class="content">This is some text</div>
```

Insert HTML code into a HTML document (other HTML code):

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

Manipulate the values of the class attribute of an element:

```php
$dom = new IvoPetkov\HTML5DOMDocument();
$dom->loadHTML('<div class="class1"></div>');

echo $dom->querySelector('div')->classList->add('class2');
```

## License
This project is licensed under the MIT License. See the [license file](https://github.com/ivopetkov/html5-dom-document-php/blob/master/LICENSE) for more information.

## Contributing
Feel free to open new issues and contribute to the project. Let's make it awesome and let's do in a positive way.

## Authors
This library is created and maintained by [Ivo Petkov](https://github.com/ivopetkov/) ([ivopetkov.com](https://ivopetkov.com)) and some [awesome folks](https://github.com/ivopetkov/html5-dom-document-php/graphs/contributors).
