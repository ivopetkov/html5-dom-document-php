<?php

$classes = array(
    'IvoPetkov\HTML5DOMDocument' => __DIR__ . '/src/HTML5DOMDocument.php',
    'IvoPetkov\HTML5DOMDocument\Internal\QuerySelectors' => __DIR__ . '/src/HTML5DOMDocument/Internal/QuerySelectors.php',
    'IvoPetkov\HTML5DOMElement' => __DIR__ . '/src/HTML5DOMElement.php',
    'IvoPetkov\HTML5DOMNodeList' => __DIR__ . '/src/HTML5DOMNodeList.php'
);

spl_autoload_register(function ($class) use ($classes) {
    if (isset($classes[$class])) {
        require $classes[$class];
    }
});

