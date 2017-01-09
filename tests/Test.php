<?php

/*
 * HTML5 DOMDocument PHP library (extends DOMDocument)
 * https://github.com/ivopetkov/html5-dom-document-php
 * Copyright 2016, Ivo Petkov
 * Free to use under the MIT license.
 */

use IvoPetkov\HTML5DOMDocument;

/**
 * @runTestsInSeparateProcesses
 */
class Test extends HTML5DOMDocumentTestCase
{

    /**
     * 
     */
    public function testSaveHTML()
    {

        $testSource = function($source, $expectedSource) {
            $dom = new HTML5DOMDocument();
            $dom->loadHTML($source);
            $this->assertTrue($expectedSource === $dom->saveHTML());
        };

        $bodyContent = '<div>hello</div>';

        $source = '<!DOCTYPE html><html><body>' . $bodyContent . '</body></html>';
        $testSource($source, $source);

        $source = '<!DOCTYPE html><html><head></head><body>' . $bodyContent . '</body></html>';
        $testSource($source, $source);

        // test custom attributes
        $source = '<!DOCTYPE html><html custom-attribute="1"><head custom-attribute="2"></head><body custom-attribute="3">' . $bodyContent . '</body></html>';
        $testSource($source, $source);

        $dom = new HTML5DOMDocument();
        // without loading anything
        $this->assertTrue('<!DOCTYPE html>' === $dom->saveHTML());
    }

    /**
     * 
     */
    public function testOmitedElements()
    {
        $testSource = function($source, $expectedSource) {
            $dom = new HTML5DOMDocument();
            $dom->loadHTML($source);
            $this->assertTrue($expectedSource === $dom->saveHTML());
        };

        $bodyContent = '<div>hello</div>';

        $expectedSource = '<!DOCTYPE html><html><body>' . $bodyContent . '</body></html>';
        $testSource('<!DOCTYPE html><html><body>' . $bodyContent . '</body></html>', $expectedSource);
        $testSource('<html><body>' . $bodyContent . '</body></html>', $expectedSource);
        $testSource('<body>' . $bodyContent . '</body>', $expectedSource);
        $testSource($bodyContent, $expectedSource);

        $headContent = '<script>alert(1);</script>';

        $expectedSource = '<!DOCTYPE html><html><head>' . $headContent . '</head></html>';
        $testSource('<!DOCTYPE html><html><head>' . $headContent . '</head></html>', $expectedSource);
        $testSource('<html><head>' . $headContent . '</head></html>', $expectedSource);
        $testSource('<head>' . $headContent . '</head>', $expectedSource);
    }

    /**
     * 
     */
    public function testUTF()
    {
        $bodyContent = '<div>hello</div>'
                . '<div>здравей</div>'
                . '<div>你好</div>';
        $expectedSource = '<!DOCTYPE html><html><body>' . $bodyContent . '</body></html>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($bodyContent);
        $this->assertTrue($expectedSource === $dom->saveHTML());
    }

    /**
     * 
     */
    public function testNbspAndWhiteSpace()
    {
        $bodyContent = '<div> &nbsp; &nbsp; &nbsp; </div>'
                . '<div> &nbsp;&nbsp;&nbsp; </div>'
                . '<div> &nbsp; <span>&nbsp;</span></div>'
                . '<div>text1 text2 </div>';
        $expectedSource = '<!DOCTYPE html><html><body>' . $bodyContent . '</body></html>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($bodyContent);
        $this->assertTrue($expectedSource === $dom->saveHTML());
    }

    /**
     * 
     */
    public function testHtmlEntities()
    {
        $attributeContent = '&quot;&#8595; &amp;';
        $bodyContent = '<div data-value="' . $attributeContent . '"> &#8595; &amp; &quot; &Acirc; &rsaquo;&rsaquo;&Acirc; </div>';
        $expectedSource = '<!DOCTYPE html><html><body>' . $bodyContent . '</body></html>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($bodyContent);
        $this->assertTrue($expectedSource === $dom->saveHTML());
        $this->assertTrue(html_entity_decode($attributeContent) === $dom->querySelector('div')->getAttribute('data-value'));
        $dom->querySelector('div')->setAttribute('data-value', $attributeContent);
        $this->assertTrue($attributeContent === $dom->querySelector('div')->getAttribute('data-value'));
    }

    /**
     * 
     */
    public function testInserHTML()
    {
        // insert beforeBodyEnd
        $source = '<!DOCTYPE html><html><body>'
                . 'text1'
                . '</body></html>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($source);
        $dom->insertHTML('<html><head><meta custom="value"></head><body>'
                . '<div>text2</div>'
                . '<div>text3</div>'
                . '</body></html>');
        $expectedSource = '<!DOCTYPE html><html><head><meta custom="value"></head><body>'
                . 'text1'
                . '<div>text2</div>'
                . '<div>text3</div>'
                . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // insert afterBodyBegin
        $source = '<!DOCTYPE html><html><body>'
                . 'text1'
                . '</body></html>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($source);
        $dom->insertHTML('<html><head><meta custom="value"></head><body>'
                . '<div>text2</div>'
                . '<div>text3</div>'
                . '</body></html>', 'afterBodyBegin');
        $expectedSource = '<!DOCTYPE html><html><head><meta custom="value"></head><body>'
                . '<div>text2</div>'
                . '<div>text3</div>'
                . 'text1'
                . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // insert afterBodyBegin in empty elements
        $source = '<!DOCTYPE html><html><body></body></html>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($source);
        $dom->insertHTML('<html><head><meta custom="value"></head><body>'
                . '<div>text1</div>'
                . '<div>text2</div>'
                . '</body></html>', 'afterBodyBegin');
        $expectedSource = '<!DOCTYPE html><html><head><meta custom="value"></head><body>'
                . '<div>text1</div>'
                . '<div>text2</div>'
                . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // insert in target
        $source = '<!DOCTYPE html><html><body>'
                . '<div></div>'
                . '<div></div>'
                . '<div></div>'
                . '</body></html>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($source);
        $secondDiv = $dom->querySelectorAll('div')->item(1);
        $secondDiv->appendChild($dom->createInsertTarget('name1'));
        $dom->insertHTML('<html><head><meta custom="value"></head><body>'
                . '<div>text1</div>'
                . '<div>text2</div>'
                . '</body></html>', 'name1');
        $expectedSource = '<!DOCTYPE html><html><head><meta custom="value"></head><body>'
                . '<div></div>'
                . '<div>'
                . '<div>text1</div>'
                . '<div>text2</div>'
                . '</div>'
                . '<div></div>'
                . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Empty source
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('');
        $dom->insertHTML('<div>text1</div>');
        $expectedSource = '<!DOCTYPE html><html><body>'
                . '<div>text1</div>'
                . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // No source
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('<div>text1</div>');
        $expectedSource = '<!DOCTYPE html><html><body>'
                . '<div>text1</div>'
                . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());


        // Text
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('text1');
        $expectedSource = '<!DOCTYPE html><html><body>'
                . 'text1'
                . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Script tag
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('<script>alert(1);</script>');
        $expectedSource = '<!DOCTYPE html><html><body>'
                . '<script>alert(1);</script>'
                . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Script tag in the head
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('<head><script>alert(1);</script></head>');
        $expectedSource = '<!DOCTYPE html><html>'
                . '<head><script>alert(1);</script></head>'
                . '</html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Custom tag
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('<component></component>');
        $expectedSource = '<!DOCTYPE html><html><body>'
                . '<component></component>'
                . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());
    }

    /**
     * 
     */
    public function testEmpty()
    {

        $testSource = function($source, $expectedSource) {
            $dom = new HTML5DOMDocument();
            $dom->loadHTML($source);
            $this->assertTrue($expectedSource === $dom->saveHTML());
        };

        $source = '<!DOCTYPE html><html><head></head><body></body></html>';
        $testSource($source, $source);
        $source = '<!DOCTYPE html><html><body></body></html>';
        $testSource($source, $source);
        $source = '<!DOCTYPE html><html><head></head></html>';
        $testSource($source, $source);
        $source = '<!DOCTYPE html><html></html>';
        $testSource($source, $source);
        $source = '<!DOCTYPE html>';
        $testSource($source, $source);

        $testSource('', '<!DOCTYPE html>');
    }

    /**
     * 
     */
    public function testQuerySelector()
    {

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body>'
                . '<div id="text1" class="class1">text1</div>'
                . '<div>text2</div>'
                . '<div>'
                . '<div class="text3 class1">text3</div>'
                . '</div>'
                . '<span id="text4" class="class1 class2">text4</div>'
                . '</body></html>');

        $this->assertTrue($dom->querySelector('#text1')->innerHTML === 'text1');

        $this->assertTrue($dom->querySelectorAll('*')->length === 7); // html + body + 4 divs + 1 span
        $this->assertTrue($dom->querySelectorAll('div')->length === 4); // 4 divs
        $this->assertTrue($dom->querySelectorAll('#text1')->length === 1);
        $this->assertTrue($dom->querySelectorAll('#text1')->item(0)->innerHTML === 'text1');
        $this->assertTrue($dom->querySelectorAll('.text3')->length === 1);
        $this->assertTrue($dom->querySelectorAll('.text3')->item(0)->innerHTML === 'text3');
        $this->assertTrue($dom->querySelectorAll('div#text1')->item(0)->innerHTML === 'text1');
        $this->assertTrue($dom->querySelectorAll('span#text4')->item(0)->innerHTML === 'text4');
        $this->assertTrue($dom->querySelectorAll('div#text4')->length === 0);
        $this->assertTrue($dom->querySelectorAll('div.class1')->length === 2);
        $this->assertTrue($dom->querySelectorAll('.class1')->length === 3);
        $this->assertTrue($dom->querySelectorAll('div.class2')->length === 0);
        $this->assertTrue($dom->querySelectorAll('span.class2')->length === 1);

        $this->assertTrue($dom->querySelectorAll('unknown')->length === 0);
        $this->assertTrue($dom->querySelectorAll('unknown')->item(0) === null);
        $this->assertTrue($dom->querySelectorAll('#unknown')->length === 0);
        $this->assertTrue($dom->querySelectorAll('#unknown')->item(0) === null);
        $this->assertTrue($dom->querySelectorAll('.unknown')->length === 0);
        $this->assertTrue($dom->querySelectorAll('.unknown')->item(0) === null);
    }

    /**
     * 
     */
    public function testElementQuerySelector()
    {

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body><div id="container">'
                . '<div id="text1" class="class1">text1</div>'
                . '<div>text2</div>'
                . '<div>'
                . '<div class="text3 class1">text3</div>'
                . '</div>'
                . '<span id="text4" class="class1 class2">text4</div>'
                . '</div></body></html>');

        $this->assertTrue($dom->querySelector('#container')->querySelector('#text1')->innerHTML === 'text1');

        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('*')->length === 5); // 4 divs + 1 span
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('div')->length === 4); // 4 divs
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('#text1')->length === 1);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('#text1')->item(0)->innerHTML === 'text1');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('.text3')->length === 1);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('.text3')->item(0)->innerHTML === 'text3');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('div#text1')->item(0)->innerHTML === 'text1');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('span#text4')->item(0)->innerHTML === 'text4');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('div#text4')->length === 0);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('div.class1')->length === 2);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('.class1')->length === 3);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('div.class2')->length === 0);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('span.class2')->length === 1);

        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('unknown')->length === 0);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('unknown')->item(0) === null);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('#unknown')->length === 0);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('#unknown')->item(0) === null);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('.unknown')->length === 0);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('.unknown')->item(0) === null);
    }

    /**
     * 
     */
    public function testInnerHTML()
    {

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body>'
                . '<div>text1</div>'
                . '</body></html>');

        $this->assertTrue($dom->querySelector('body')->innerHTML === '<div>text1</div>');

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<div>text1</div>');
        $element = $dom->querySelector('div');
        $element->innerHTML = 'text2';
        $this->assertTrue('<!DOCTYPE html><html><body><div>text2</div></body></html>' === $dom->saveHTML());

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<div>text1</div>');
        $element = $dom->querySelector('div');
        $element->innerHTML = '<div>text1<div>text2</div></div>';
        $this->assertTrue('<!DOCTYPE html><html><body><div><div>text1<div>text2</div></div></div></body></html>' === $dom->saveHTML());
    }

    /**
     * 
     */
    public function testOuterHTML()
    {

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body>'
                . '<div>text1</div>'
                . '</body></html>');

        $this->assertTrue($dom->querySelector('div')->outerHTML === '<div>text1</div>');
        $this->assertTrue((string) $dom->querySelector('div') === '<div>text1</div>');

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<div>text1</div>');
        $element = $dom->querySelector('div');
        $element->outerHTML = 'text2';
        $this->assertTrue('<!DOCTYPE html><html><body>text2</body></html>' === $dom->saveHTML());

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<div>text1</div>');
        $element = $dom->querySelector('div');
        $element->outerHTML = '<div>text2<div>text3</div></div>';
        $this->assertTrue('<!DOCTYPE html><html><body><div>text2<div>text3</div></div></body></html>' === $dom->saveHTML());
    }

    /**
     * 
     */
    public function testGetAttributes()
    {

        $dataAttributeValue = '&quot;<>&*;';
        $expectedDataAttributeValue = '"<>&*;';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body>'
                . '<div class="text1" data-value="' . $dataAttributeValue . '">text1</div>'
                . '</body></html>');

        $this->assertTrue($dom->querySelector('div')->getAttribute('class') === 'text1');
        $this->assertTrue($dom->querySelector('div')->getAttribute('unknown') === '');
        $this->assertTrue($dom->querySelector('div')->getAttribute('data-value') === $expectedDataAttributeValue);
        $attributes = $dom->querySelector('div')->getAttributes();
        $this->assertTrue(sizeof($attributes) === 2);
        $this->assertTrue($attributes['class'] === 'text1');
    }

    /**
     * 
     */
    public function testFiles()
    {

        $filename = sys_get_temp_dir() . '/html5-dom-document-test-file-' . md5(uniqid());
        file_put_contents($filename, '<!DOCTYPE html><html><body>'
                . '<div>text1</div>'
                . '<div>text2</div>'
                . '</body></html>');
        $dom = new HTML5DOMDocument();
        $dom->loadHTMLFile($filename);
        $dom->querySelector('body')->removeChild($dom->querySelector('div')); // remove first div
        $dom->saveHTMLFile($filename);
        $this->assertTrue(file_get_contents($filename) === '<!DOCTYPE html><html><body>'
                . '<div>text2</div>'
                . '</body></html>');
    }

    /**
     * 
     */
    public function testDuplicateIDs()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><html><head>'
                . '<script id="script1">var script1=1;</script>'
                . '<script id="script2">var script2=1;</script>'
                . '</head><body>'
                . 'hello<div id="text1">text1</div>'
                . '<div id="text2">text2</div>'
                . '<div id="text3">text3</div>'
                . '</body></html>');
        $dom->insertHTML('<!DOCTYPE html><html><head>'
                . '<script id="script0">var script0=1;</script>'
                . '<script id="script1">var script1=1;</script>'
                . '<script id="script3">var script3=1;</script>'
                . '</head><body>'
                . '<div id="text0">text0</div>'
                . '<div id="text2">text2</div>'
                . '<div id="text4">text4</div>'
                . '</body></html>');
        $expectedSource = '<!DOCTYPE html><html><head>'
                . '<script id="script1">var script1=1;</script>'
                . '<script id="script2">var script2=1;</script>'
                . '<script id="script0">var script0=1;</script>'
                . '<script id="script3">var script3=1;</script>'
                . '</head><body>'
                . 'hello<div id="text1">text1</div>'
                . '<div id="text2">text2</div>'
                . '<div id="text3">text3</div>'
                . '<div id="text0">text0</div>'
                . '<div id="text4">text4</div>'
                . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());
    }

    /**
     * 
     */
    public function testDuplicateTags()
    {

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><html><head>'
                . '<title>Title1</title>'
                . '</head></html>');
        $dom->insertHTML('<head>'
                . '<title>Title2</title>'
                . '</head>');
        $expectedSource = '<!DOCTYPE html><html><head>'
                . '<title>Title2</title>'
                . '</head></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><html><head>'
                . '<meta charset="utf-8">'
                . '<meta content="index,follow" name="robots">'
                . '<meta content="html5" name="keywords">'
                . '<meta content="website" property="og:type">'
                . '</head></html>');
        $dom->insertHTML('<head>'
                . '<meta content="dom" name="keywords">'
                . '<meta charset="us-ascii">'
                . '<meta content="video.movie" property="og:type">'
                . '</head>');
        $expectedSource = '<!DOCTYPE html><html><head>'
                . '<meta content="index,follow" name="robots">'
                . '<meta content="dom" name="keywords">'
                . '<meta charset="us-ascii">'
                . '<meta content="video.movie" property="og:type">'
                . '</head></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());
    }

    /**
     * 
     */
    public function testMultipleHeadAndBodyTags()
    {

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><html>'
                . '<head>'
                . '<title>Title1</title>'
                . '<meta charset="utf-8">'
                . '</head>'
                . '<head>'
                . '<title>Title2</title>'
                . '<meta content="index,follow" name="robots">'
                . '</head>'
                . '<body>'
                . 'Text1'
                . '<div>TextA</div>'
                . '</body>'
                . '<body>'
                . 'Text2'
                . '<div>TextB</div>'
                . '</body>'
                . '</html>');
        $expectedSource = '<!DOCTYPE html><html>'
                . '<head>'
                . '<meta charset="utf-8">'
                . '<title>Title2</title>'
                . '<meta content="index,follow" name="robots">'
                . '</head>'
                . '<body>'
                . 'Text1'
                . '<div>TextA</div>'
                . 'Text2'
                . '<div>TextB</div>'
                . '</body>'
                . '</html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());
    }

    /**
     * 
     */
    public function testInsertHTMLCopyAttributes()
    {

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html>'
                . '<html data-html-custom-1="1">'
                . '<head data-head-custom-1="1"></head>'
                . '<body data-body-custom-1="1"></body>'
                . '</html>');
        $dom->insertHTML('<html data-html-custom-1="A" data-html-custom-2="B">'
                . '<head data-head-custom-1="A" data-head-custom-2="B"></head>'
                . '<body data-body-custom-1="A" data-body-custom-2="B"></body>'
                . '</html>');
        $expectedSource = '<!DOCTYPE html>'
                . '<html data-html-custom-1="A" data-html-custom-2="B">'
                . '<head data-head-custom-1="A" data-head-custom-2="B"></head>'
                . '<body data-body-custom-1="A" data-body-custom-2="B"></body>'
                . '</html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());
    }

    /**
     * 
     */
    public function testInvalidArguments1()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><body></body></html>');
        $element = $dom->querySelector('body');
        $this->setExpectedException('\Exception');
        $element->missing;
    }

    /**
     * 
     */
    public function testInvalidArguments2()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><body></body></html>');
        $element = $dom->querySelector('body');
        $this->setExpectedException('\Exception');
        $element->missing = 'true';
    }

    /**
     * 
     */
    public function testInvalidArguments3()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><body></body></html>');
        $element = $dom->querySelector('body');
        $this->setExpectedException('\InvalidArgumentException');
        $element->innerHTML = 1;
    }

    /**
     * 
     */
    public function testInvalidArguments4()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><body></body></html>');
        $element = $dom->querySelector('body');
        $this->setExpectedException('\InvalidArgumentException');
        $element->getAttribute(1);
    }

    /**
     * 
     */
    public function testInvalidArguments5()
    {
        $list = new \IvoPetkov\HTML5DOMNodeList();
        $this->setExpectedException('\Exception');
        $list->missing;
    }

}
