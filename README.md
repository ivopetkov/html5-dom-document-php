# HTML5DOMDocument

**An HTML5 parser for PHP**

HTML5DOMDocument extends the native [DOMDocument](http://php.net/manual/en/class.domdocument.php) library and fixes some bugs, and adds some new functionality. Learn more at [https://ivopetkov.com/](https://ivopetkov.com/).

## Familiar

HTML5DOMDocument is really easy to use - just like you should use DOMDocument.
```php
<?php
require 'vendor/autoload.php';

$dom = new IvoPetkov\HTML5DOMDocument();
$dom->loadHTML('<!DOCTYPE html><html><body>Hello</body></html>');
echo $dom->saveHTML();
```

## Download and install

* Install via Composer
```
composer require ivopetkov/html5-dom-document-php
```

## License
HTML5DOMDocument is open-sourced software. It's free to use under the MIT license. See the [license file](https://github.com/ivopetkov/html5-dom-document-php/blob/master/LICENSE) for more information.

## Let's talk
You can find me at [@IvoPetkovCom](https://twitter.com/IvoPetkovCom) and [ivopetkov.com](http://ivopetkov.com)
