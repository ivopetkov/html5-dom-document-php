<?php

/*
 * HTML5 DOMDocument PHP library (extends DOMDocument)
 * https://github.com/ivopetkov/html5-dom-document-php
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

use IvoPetkov\HTML5DOMDocument;
use IvoPetkov\HTML5DOMElement;

/**
 * @runTestsInSeparateProcesses
 */
class Test extends PHPUnit\Framework\TestCase
{

    /**
     *
     */
    public function testSaveHTML()
    {

        $testSource = function ($source, $expectedSource) {
            $dom = new HTML5DOMDocument();
            $dom->loadHTML($source);
            $this->assertTrue($expectedSource === $dom->saveHTML());
        };

        $bodyContent = '<div>hello</div>';

        $source = '<!DOCTYPE html>' . "\n" . '<html><body>' . $bodyContent . '</body></html>';
        $testSource($source, $source);

        $source = '<!DOCTYPE html>' . "\n" . '<html><head></head><body>' . $bodyContent . '</body></html>';
        $testSource($source, $source);

        // test custom attributes
        $source = '<!DOCTYPE html>' . "\n" . '<html custom-attribute="1"><head custom-attribute="2"></head><body custom-attribute="3">' . $bodyContent . '</body></html>';
        $testSource($source, $source);

        $dom = new HTML5DOMDocument();
        // without loading anything
        $this->assertTrue('' === $dom->saveHTML());
    }

    /**
     *
     */
    public function testOmitedElements()
    {
        $testSource = function ($source, $expectedSource) {
            $dom = new HTML5DOMDocument();
            $dom->loadHTML($source);
            $this->assertEquals($expectedSource, $dom->saveHTML());
        };

        $bodyContent = '<div>hello</div>';

        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>' . $bodyContent . '</body></html>';
        $testSource('<!DOCTYPE html><html><body>' . $bodyContent . '</body></html>', $expectedSource);
        $testSource('<html><body>' . $bodyContent . '</body></html>', $expectedSource);
        $testSource('<body>' . $bodyContent . '</body>', $expectedSource);
        $testSource($bodyContent, $expectedSource);

        $headContent = '<script>alert(1);</script>';

        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><head>' . $headContent . '</head></html>';
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
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>' . $bodyContent . '</body></html>';
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
        // Has problems with:
        //    <label>Label 1</label>
        //    <input>
        //    <label>Label 2</label>
        //    <input>
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>' . $bodyContent . '</body></html>';
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
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>' . $bodyContent . '</body></html>';
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
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><head><meta custom="value"></head><body>'
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
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><head><meta custom="value"></head><body>'
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
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><head><meta custom="value"></head><body>'
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
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><head><meta custom="value"></head><body>'
            . '<div></div>'
            . '<div>'
            . '<div>text1</div>'
            . '<div>text2</div>'
            . '</div>'
            . '<div></div>'
            . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // insert in target in empty dom
        $dom = new HTML5DOMDocument();
        $insertTarget = $dom->createInsertTarget('name1');
        $dom->insertHTML('<body></body>');
        $dom->querySelector('body')->appendChild($insertTarget);
        $dom->insertHTML('value1', 'name1');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>value1</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Insert duplicate ID
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<div>1</div>'
            . '<div id="value1">2</div>'
            . '<div>3</div>');
        $dom->insertHTML('<div id="value1">5</div><div>4</div>');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>'
            . '<div>1</div>'
            . '<div id="value1">2</div>'
            . '<div>3</div>'
            . '<div id="value1">5</div>'
            . '<div>4</div>'
            . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Empty source
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('');
        $dom->insertHTML('<div>text1</div>');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>'
            . '<div>text1</div>'
            . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // No source
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('<div>text1</div>');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>'
            . '<div>text1</div>'
            . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Text
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('text1');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>'
            . 'text1'
            . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Script tag
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('<script>alert(1);</script>');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>'
            . '<script>alert(1);</script>'
            . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Script tag in the head
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('<head><script>alert(1);</script></head>');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html>'
            . '<head><script>alert(1);</script></head>'
            . '</html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Custom tag
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('<component></component>');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>'
            . '<component></component>'
            . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Empty content
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('');
        $expectedSource = '<!DOCTYPE html>' . "\n";
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Html tag with attribute
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('<html data-var1="value1"></html>');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html data-var1="value1"></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Head tag with attribute
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('<head data-var1="value1"></head>');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><head data-var1="value1"></head></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Body tag with attribute
        $dom = new HTML5DOMDocument();
        $dom->insertHTML('<body data-var1="value1"></body>');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body data-var1="value1"></body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        // Empty content in insert target
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<body></body>');
        $insertTarget = $dom->createInsertTarget('name1');
        $dom->querySelector('body')->appendChild($insertTarget);
        $dom->insertHTML('', 'name1');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body></body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());
    }

    /**
     *
     */
    public function testEmpty()
    {

        $testSource = function ($source, $expectedSource) {
            $dom = new HTML5DOMDocument();
            $dom->loadHTML($source);
            $this->assertTrue($expectedSource === $dom->saveHTML());
        };

        $source = '<!DOCTYPE html>' . "\n" . '<html><head></head><body></body></html>';
        $testSource($source, $source);
        $source = '<!DOCTYPE html>' . "\n" . '<html><body></body></html>';
        $testSource($source, $source);
        $source = '<!DOCTYPE html>' . "\n" . '<html><head></head></html>';
        $testSource($source, $source);
        $source = '<!DOCTYPE html>' . "\n" . '<html></html>';
        $testSource($source, $source);
        $source = '<!DOCTYPE html>' . "\n";
        $testSource($source, $source);

        $testSource('', '<!DOCTYPE html>' . "\n");
    }

    /**
     *
     */
    public function testQuerySelector()
    {

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body>'
            . '<h1>text0</h1>'
            . '<div id="text1" class="class1">text1</div>'
            . '<div>text2</div>'
            . '<div>'
            . '<div class="text3 class1">text3</div>'
            . '</div>'
            . '<my-custom-element class="text5 class1">text5</my-custom-element>'
            . '<span id="text4" class="class1 class2">text4</div>'
            . '</body></html>');

        $this->assertTrue($dom->querySelector('#text1')->innerHTML === 'text1');

        $this->assertTrue($dom->querySelectorAll('*')->length === 9); // html + body + 1 h1 + 4 divs + 1 custom element + 1 span
        $this->assertTrue($dom->querySelectorAll('h1')->item(0)->innerHTML === 'text0');
        $this->assertTrue($dom->querySelectorAll('div')->length === 4); // 4 divs
        $this->assertTrue($dom->querySelectorAll('#text1')->length === 1);
        $this->assertTrue($dom->querySelectorAll('#text1')->item(0)->innerHTML === 'text1');
        $this->assertTrue($dom->querySelectorAll('.text3')->length === 1);
        $this->assertTrue($dom->querySelectorAll('.text3')->item(0)->innerHTML === 'text3');
        $this->assertTrue($dom->querySelectorAll('div#text1')->item(0)->innerHTML === 'text1');
        $this->assertTrue($dom->querySelectorAll('span#text4')->item(0)->innerHTML === 'text4');
        $this->assertTrue($dom->querySelectorAll('[id="text4"]')->item(0)->innerHTML === 'text4');
        $this->assertTrue($dom->querySelectorAll('span[id="text4"]')->item(0)->innerHTML === 'text4');
        $this->assertTrue($dom->querySelectorAll('[id]')->item(0)->innerHTML === 'text1');
        $this->assertTrue($dom->querySelectorAll('[id]')->length === 2);
        $this->assertTrue($dom->querySelectorAll('span[id]')->item(0)->innerHTML === 'text4');
        $this->assertTrue($dom->querySelectorAll('span[data-other]')->length === 0);
        $this->assertTrue($dom->querySelectorAll('div#text4')->length === 0);
        $this->assertTrue($dom->querySelectorAll('div.class1')->length === 2);
        $this->assertTrue($dom->querySelectorAll('.class1')->length === 4);
        $this->assertTrue($dom->querySelectorAll('.class1.class2')->length === 1);
        $this->assertTrue($dom->querySelectorAll('.class2.class1')->length === 1);
        $this->assertTrue($dom->querySelectorAll('div.class2')->length === 0);
        $this->assertTrue($dom->querySelectorAll('span.class2')->length === 1);
        $this->assertTrue($dom->querySelectorAll('my-custom-element')->length === 1);
        $this->assertTrue($dom->querySelectorAll('my-custom-element.text5')->length === 1);
        $this->assertTrue($dom->querySelectorAll('my-custom-element.text5')->item(0)->innerHTML === 'text5');

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
        $dom->loadHTML('<html><head>'
            . '<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">'
            . '<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32"></head>'
            . '<body><div id="container">'
            . '<div id="text1" class="class1">text1</div>'
            . '<div>text2</div>'
            . '<div>'
            . '<div class="class3 class1">text3</div>'
            . '</div>'
            . '<my-custom-element class="class5 class1">text5</my-custom-element>'
            . '<span id="text4" class="class1 class2">text4</div>'
            . '</div></body></html>');

        $this->assertTrue($dom->querySelector('#container')->querySelector('#text1')->innerHTML === 'text1');

        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('*')->length === 6); // 4 divs + 1 custom element + 1 span
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('div')->length === 4); // 4 divs
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('#text1')->length === 1);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('#text1')->item(0)->innerHTML === 'text1');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('.class3')->length === 1);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('.class3')->item(0)->innerHTML === 'text3');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('[class~="class3"]')->length === 1);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('[class~="class3"]')->item(0)->innerHTML === 'text3');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('[class|="class1"]')->length === 1);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('[class|="class1"]')->item(0)->innerHTML === 'text1');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('[class^="class3"]')->length === 1);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('[class^="class3"]')->item(0)->innerHTML === 'text3');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('[class$="class2"]')->length === 1);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('[class$="class2"]')->item(0)->innerHTML === 'text4');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('[class*="ss3"]')->length === 1);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('[class*="ss3"]')->item(0)->innerHTML === 'text3');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('div#text1')->item(0)->innerHTML === 'text1');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('span#text4')->item(0)->innerHTML === 'text4');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('[id="text4"]')->item(0)->innerHTML === 'text4');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('span[id="text4"]')->item(0)->innerHTML === 'text4');
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('div#text4')->length === 0);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('div.class1')->length === 2);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('.class1')->length === 4);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('div.class2')->length === 0);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('span.class2')->length === 1);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('my-custom-element')->length === 1);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('my-custom-element.class5')->length === 1);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('my-custom-element.class5')->item(0)->innerHTML === 'text5');

        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('unknown')->length === 0);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('unknown')->item(0) === null);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('#unknown')->length === 0);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('#unknown')->item(0) === null);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('.unknown')->length === 0);
        $this->assertTrue($dom->querySelector('#container')->querySelectorAll('.unknown')->item(0) === null);

        $this->assertEquals('/favicon-16x16.png', $dom->querySelectorAll('link[rel="icon"]')->item(0)->getAttribute('href'));
        $this->assertEquals('/favicon-32x32.png', $dom->querySelectorAll('link[rel="icon"]')->item(1)->getAttribute('href'));
        $this->assertEquals('/favicon-16x16.png', $dom->querySelectorAll('link[rel="icon"][sizes="16x16"]')->item(0)->getAttribute('href'));
        $this->assertNull($dom->querySelectorAll('link[rel="icon"][sizes="16x16"]')->item(1));
    }

    /**
     *
     */
    public function testElementQuerySelectorCaseSensitivity()
    {

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body>' .
            '<dIV class="claSS1" id="elemeNT1">' .
            '<div class="claSS2 claSS3">' .
            '<spAN>text1</span>' .
            '<A>text2</a>' .
            '</div>' .
            '<a>text3</a>' .
            '<a>text4</a>' .
            '</div>' .
            '</body></html>');

        $this->assertEquals($dom->querySelector('div')->innerHTML, '<div class="claSS2 claSS3"><span>text1</span><a>text2</a></div><a>text3</a><a>text4</a>');
        $this->assertEquals($dom->querySelector('Div')->innerHTML, '<div class="claSS2 claSS3"><span>text1</span><a>text2</a></div><a>text3</a><a>text4</a>');
        $this->assertEquals($dom->querySelector('span')->innerHTML, 'text1');
        $this->assertEquals($dom->querySelector('Span')->innerHTML, 'text1');
        $this->assertNull($dom->querySelector('div[class="class1"]'));
        $this->assertEquals($dom->querySelector('div[class="claSS1"]')->innerHTML, '<div class="claSS2 claSS3"><span>text1</span><a>text2</a></div><a>text3</a><a>text4</a>');
        $this->assertEquals($dom->querySelector('Div[Class="claSS1"]')->innerHTML, '<div class="claSS2 claSS3"><span>text1</span><a>text2</a></div><a>text3</a><a>text4</a>');
        $this->assertNull($dom->querySelector('div#element1'));
        $this->assertEquals($dom->querySelector('div#elemeNT1')->innerHTML, '<div class="claSS2 claSS3"><span>text1</span><a>text2</a></div><a>text3</a><a>text4</a>');
        $this->assertNull($dom->querySelector('Div#element1'));
        $this->assertEquals($dom->querySelector('Div#elemeNT1')->innerHTML, '<div class="claSS2 claSS3"><span>text1</span><a>text2</a></div><a>text3</a><a>text4</a>');
        $this->assertNull($dom->querySelector('#element1'));
        $this->assertEquals($dom->querySelector('#elemeNT1')->innerHTML, '<div class="claSS2 claSS3"><span>text1</span><a>text2</a></div><a>text3</a><a>text4</a>');
        $this->assertNull($dom->querySelector('div.class2.class3'));
        $this->assertEquals($dom->querySelector('div.claSS2.claSS3')->innerHTML, '<span>text1</span><a>text2</a>');
        $this->assertNull($dom->querySelector('.class2.class3'));
        $this->assertEquals($dom->querySelector('.claSS2.claSS3')->innerHTML, '<span>text1</span><a>text2</a>');
    }

    /**
     *
     */
    public function testComplexQuerySelectors()
    {

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body>'
            . '<span>text1</span>'
            . '<span>text2</span>'
            . '<span>text3</span>'
            . '<div><span>text4</span></div>'
            . '<div id="id,1">text5</div>'
            . '<a href="#">text6</a>'
            . '<div"><a href="#">text7</a></div>'
            . '</body></html>');

        $this->assertTrue($dom->querySelectorAll('span, div')->length === 7); // 4 spans + 3 divs
        $this->assertTrue($dom->querySelectorAll('span, [id="id,1"]')->length === 5); // 4 spans + 1 div
        $this->assertTrue($dom->querySelectorAll('div, [id="id,1"]')->length === 3); // 3 divs

        $this->assertTrue($dom->querySelectorAll('body div')->length === 3);
        $this->assertTrue($dom->querySelectorAll('body a')->length === 2);

        $this->assertTrue($dom->querySelectorAll('body > a')->length === 1);
        $this->assertTrue($dom->querySelector('body > a')->innerHTML === 'text6');
        $this->assertTrue($dom->querySelectorAll('div > a')->length === 1);
        $this->assertTrue($dom->querySelector('div > a')->innerHTML === 'text7');

        $this->assertTrue($dom->querySelectorAll('span + span')->length === 2);
        $this->assertTrue($dom->querySelectorAll('span + span')[0]->innerHTML === 'text2');
        $this->assertTrue($dom->querySelectorAll('span + span')[1]->innerHTML === 'text3');

        $this->assertTrue($dom->querySelectorAll('span ~ div')->length === 3);
    }

    /**
     * Tests multiple query selectors matching. If a query selector is not greedy problems may arise.
     */
    public function testComplexQuerySelectors2()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<body>'
            . '<div class="a1">1</div>'
            . '<div class="a2">2</div>'
            . '<div class="a3">3</div>'
            . '</body>');
        $elements = $dom->querySelectorAll('.a1,.a2,.a3');
        $this->assertEquals($elements->length, 3);
    }

    /**
     * Test simple selectors if greedy
     */
    public function testComplexQuerySelectors3()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body><div class="class1" id="element1"><div><span>text1</span><a>text2</a></div><a>text3</a><a>text4</a></div></body></html>');
        $this->assertEquals($dom->querySelector('div.class1 > div > a')->innerHTML, 'text2');
        $this->assertEquals($dom->querySelector('div.class1 > a')->innerHTML, 'text3');
        $this->assertEquals($dom->querySelector('div[class="class1"] > div > a')->innerHTML, 'text2');
        $this->assertEquals($dom->querySelector('div[class="class1"] > a')->innerHTML, 'text3');
        $this->assertEquals($dom->querySelector('div#element1 > div > a')->innerHTML, 'text2');
        $this->assertEquals($dom->querySelector('div#element1 > a')->innerHTML, 'text3');
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
        $this->assertTrue('<!DOCTYPE html>' . "\n" . '<html><body><div>text2</div></body></html>' === $dom->saveHTML());

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<div>text1</div>');
        $element = $dom->querySelector('div');
        $element->innerHTML = '<div>text1<div>text2</div></div>';
        $this->assertTrue('<!DOCTYPE html>' . "\n" . '<html><body><div><div>text1<div>text2</div></div></div></body></html>' === $dom->saveHTML());
    }

    /**
     *
     */
    public function testOuterHTML()
    {

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body>'
            . '<div>text1</div><span title="hi"></span><br/>'
            . '</body></html>');

        $this->assertTrue($dom->querySelector('div')->outerHTML === '<div>text1</div>');
        $this->assertTrue((string) $dom->querySelector('div') === '<div>text1</div>');

        $this->assertTrue($dom->querySelector('span')->outerHTML === '<span title="hi"></span>');
        $this->assertTrue((string) $dom->querySelector('span') === '<span title="hi"></span>');

        $this->assertTrue($dom->querySelector('br')->outerHTML === '<br/>');
        $this->assertTrue((string) $dom->querySelector('br') === '<br/>');

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<div>text1</div>');
        $element = $dom->querySelector('div');
        $element->outerHTML = 'text2';
        $this->assertTrue('<!DOCTYPE html>' . "\n" . '<html><body>text2</body></html>' === $dom->saveHTML());

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<div>text1</div>');
        $element = $dom->querySelector('div');
        $element->outerHTML = '<div>text2<div>text3</div></div>';
        $this->assertTrue('<!DOCTYPE html>' . "\n" . '<html><body><div>text2<div>text3</div></div></body></html>' === $dom->saveHTML());
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
        $this->assertTrue(file_get_contents($filename) === '<!DOCTYPE html>' . "\n" . '<html><body>'
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
            . '<script id="script1">var script1=2;</script>'
            . '</head><body></body></html>', HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><head>'
            . '<script id="script1">var script1=1;</script>'
            . '<script id="script1">var script1=2;</script>'
            . '</head><body></body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><html><head>'
            . '<script id="script1">var script1=1;</script>'
            . '<script id="script2">var script2=1;</script>'
            . '</head><body>'
            . 'hello<div id="text1">text1</div>'
            . '<div id="text2">text2</div>'
            . '<div id="text3">text3</div>'
            . '<div><span id="span1">hi1</span></div>'
            . '<span id="span2">hi2</span>'
            . '</body></html>');
        $dom->insertHTML('<!DOCTYPE html><html><head>'
            . '<script id="script0">var script0=1;</script>'
            . '<script id="script1">var script1=1;</script>'
            . '<script id="script3">var script3=1;</script>'
            . '</head><body>'
            . '<div id="text0">text0</div>'
            . '<div id="text2">text2</div>'
            . '<div id="text4">text4</div>'
            . '<span id="span1">hi11</span>'
            . '<div><span id="span1">hi22</span></div>'
            . '</body></html>');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><head>'
            . '<script id="script1">var script1=1;</script>'
            . '<script id="script2">var script2=1;</script>'
            . '<script id="script0">var script0=1;</script>'
            . '<script id="script1">var script1=1;</script>'
            . '<script id="script3">var script3=1;</script>'
            . '</head><body>'
            . 'hello<div id="text1">text1</div>'
            . '<div id="text2">text2</div>'
            . '<div id="text3">text3</div>'
            . '<div><span id="span1">hi1</span></div>'
            . '<span id="span2">hi2</span>'
            . '<div id="text0">text0</div>'
            . '<div id="text2">text2</div>'
            . '<div id="text4">text4</div>'
            . '<span id="span1">hi11</span>'
            . '<div><span id="span1">hi22</span></div>'
            . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><html><body>'
            . '<div id="text1">text1</div>'
            . '</body></html>');
        $dom->insertHTML('<!DOCTYPE html><html><body>'
            . '<div>'
            . '<div id="text1">text1</div>'
            . '<div><div id="text1">text1</div></div>'
            . '<div id="text2">text2</div>'
            . '</div>'
            . '</body></html>');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>'
            . '<div id="text1">text1</div>'
            . '<div>'
            . '<div id="text1">text1</div>'
            . '<div><div id="text1">text1</div></div>'
            . '<div id="text2">text2</div>'
            . '</div>'
            . '</body></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());

        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><html><body>'
            . '<div id="text1">text1</div>'
            . '</body></html>');
        $dom->insertHTML('<!DOCTYPE html><html><body>'
            . '<div>'
            . '<div id="text2">text2</div>'
            . '<div id="text2">text2</div>'
            . '</div>'
            . '</body></html>');
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>'
            . '<div id="text1">text1</div>'
            . '<div>'
            . '<div id="text2">text2</div>'
            . '<div id="text2">text2</div>'
            . '</div>'
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
        $dom->modify(HTML5DOMDocument::FIX_MULTIPLE_TITLES);
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><head>'
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
            . '<title>Title1</title>'
            . '</head>');
        $dom->modify(
            HTML5DOMDocument::FIX_DUPLICATE_METATAGS |
                HTML5DOMDocument::OPTIMIZE_HEAD
        );
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><head>'
            . '<meta charset="us-ascii">'
            . '<title>Title1</title>'
            . '<meta content="index,follow" name="robots">'
            . '<meta content="dom" name="keywords">'
            . '<meta content="video.movie" property="og:type">'
            . '</head></html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());
    }

    /**
     *
     */
    public function testSaveHTMLForNodes()
    {
        // A custom html tags makes the default saveHTML function return more whitespaces
        $html = '<html><head><component><script src="url1"/><script src="url2"/></component></head><body><div><component><ul><li><a href="#">Link 1</a></li><li><a href="#">Link 2</a></li></ul></component></div>';

        $dom = new HTML5DOMDocument();
        $dom->loadHTML($html);

        $expectedOutput = '<div><component><ul><li><a href="#">Link 1</a></li><li><a href="#">Link 2</a></li></ul></component></div>';
        $this->assertTrue($dom->saveHTML($dom->querySelector('div')) === $expectedOutput);

        $expectedOutput = '<body><div><component><ul><li><a href="#">Link 1</a></li><li><a href="#">Link 2</a></li></ul></component></div></body>';
        $this->assertTrue($dom->saveHTML($dom->querySelector('div')->parentNode) === $expectedOutput);

        $expectedOutput = '<html><head><component><script src="url1"></script><script src="url2"></script></component></head><body><div><component><ul><li><a href="#">Link 1</a></li><li><a href="#">Link 2</a></li></ul></component></div></body></html>';
        $this->assertTrue($dom->saveHTML($dom->querySelector('div')->parentNode->parentNode) === $expectedOutput);

        $expectedOutput = '<!DOCTYPE html>' . "\n" . '<html><head><component><script src="url1"></script><script src="url2"></script></component></head><body><div><component><ul><li><a href="#">Link 1</a></li><li><a href="#">Link 2</a></li></ul></component></div></body></html>';
        $this->assertTrue($dom->saveHTML($dom->querySelector('div')->parentNode->parentNode->parentNode) === $expectedOutput);

        $expectedOutput = '<script src="url1"></script>';
        $this->assertTrue($dom->saveHTML($dom->querySelector('script')) === $expectedOutput);

        $expectedOutput = '<component><script src="url1"></script><script src="url2"></script></component>';
        $this->assertTrue($dom->saveHTML($dom->querySelector('script')->parentNode) === $expectedOutput);

        $expectedOutput = '<head><component><script src="url1"></script><script src="url2"></script></component></head>';
        $this->assertTrue($dom->saveHTML($dom->querySelector('script')->parentNode->parentNode) === $expectedOutput);

        $expectedOutput = '<html><head><component><script src="url1"></script><script src="url2"></script></component></head><body><div><component><ul><li><a href="#">Link 1</a></li><li><a href="#">Link 2</a></li></ul></component></div></body></html>';
        $this->assertTrue($dom->saveHTML($dom->querySelector('script')->parentNode->parentNode->parentNode) === $expectedOutput);

        $expectedOutput = '<!DOCTYPE html>' . "\n" . '<html><head><component><script src="url1"></script><script src="url2"></script></component></head><body><div><component><ul><li><a href="#">Link 1</a></li><li><a href="#">Link 2</a></li></ul></component></div></body></html>';
        $this->assertTrue($dom->saveHTML($dom->querySelector('script')->parentNode->parentNode->parentNode->parentNode) === $expectedOutput);
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
        $dom->modify(
            HTML5DOMDocument::FIX_MULTIPLE_HEADS |
                HTML5DOMDocument::FIX_MULTIPLE_BODIES |
                HTML5DOMDocument::FIX_MULTIPLE_TITLES
        );
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html>'
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
        $expectedSource = '<!DOCTYPE html>' . "\n" . ''
            . '<html data-html-custom-1="A" data-html-custom-2="B">'
            . '<head data-head-custom-1="A" data-head-custom-2="B"></head>'
            . '<body data-body-custom-1="A" data-body-custom-2="B"></body>'
            . '</html>';
        $this->assertTrue($expectedSource === $dom->saveHTML());
    }

    public function testInsertHTMLMulti()
    {
        $html = '';
        for ($i = 0; $i < 5; $i++) {
            $html .= '<div>';
            $html .= '<div id="id' . $i . '"></div>';
            $html .= '<div class="class' . $i . '"></div>';
            $html .= '<div></div>';
            $html .= '<div></div>';
            $html .= '<div></div>';
            $html .= '</div>';
        }

        $dom1 = new IvoPetkov\HTML5DOMDocument();
        $dom1->loadHTML('<body></body>');
        for ($i = 0; $i < 5; $i++) {
            $dom1->insertHTML($html);
        }
        $result1 = $dom1->saveHTML();

        $dom2 = new IvoPetkov\HTML5DOMDocument();
        $dom2->loadHTML('<body></body>');

        $data = [];
        for ($i = 0; $i < 5; $i++) {
            $data[] = ['source' => $html];
        }
        $dom2->insertHTMLMulti($data);
        $result2 = $dom2->saveHTML();
        $this->assertTrue($result1 === $result2);
    }

    /**
     *
     */
    public function testInvalidArguments1()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><body></body></html>');
        $element = $dom->querySelector('body');
        $this->expectException('\Exception');
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
        $this->expectException('\Exception');
        $element->missing = 'true';
    }

    /**
     *
     */
    public function testInvalidArguments5()
    {
        $list = new \IvoPetkov\HTML5DOMNodeList();
        $this->expectException('\Exception');
        $list->missing;
    }

    /**
     * @group classList
     */
    public function testClassListContains()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body class=" c aaa b  c  "></body></html>');

        $html = $dom->querySelector('html');
        $this->assertFalse($html->classList->contains('a'));

        $body = $dom->querySelector('body');
        $classList = $body->classList;
        $this->assertFalse($classList->contains('a'));
        $this->assertTrue($classList->contains('aaa'));
        $this->assertTrue($classList->contains('b'));
        $this->assertTrue($classList->contains('c'));
        $this->assertFalse($classList->contains('d'));
    }

    /**
     * @group classList
     */
    public function testClassListEntries()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body class="  a   b c b a c"></body></html>');

        $text = '';
        $html = $dom->querySelector('html');
        foreach ($html->classList->entries() as $class) {
            $text .= "[$class]";
        }
        $this->assertSame('', $text);

        $text = '';
        $body = $dom->querySelector('body');
        foreach ($body->classList->entries() as $class) {
            $text .= "[$class]";
        }
        $this->assertSame('[a][b][c]', $text);
    }

    /**
     * @group classList
     */
    public function testClassListItem()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body class="  a   b c b a c"></body></html>');

        $html = $dom->querySelector('html');
        $this->assertNull($html->classList->item(0));
        $this->assertNull($html->classList->item(1));

        $body = $dom->querySelector('body');
        $this->assertSame('a', $body->classList->item(0));
        $this->assertSame('b', $body->classList->item(1));
        $this->assertSame('c', $body->classList->item(2));
        $this->assertNull($body->classList->item(3));
    }

    /**
     * @group classList
     */
    public function testClassListAdd()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body class="  a   b c b a c"></body></html>');

        $html = $dom->querySelector('html');
        $html->classList->add('abc');
        $this->assertSame('abc', $html->getAttribute('class'));

        $body = $dom->querySelector('body');
        $body->classList->add('a', 'd');
        $this->assertSame('a b c d', $body->getAttribute('class'));
    }

    /**
     * @group classList
     */
    public function testClassListRemove()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body class="  a   b c b a c"></body></html>');

        $html = $dom->querySelector('html');
        $html->classList->remove('a');
        $this->assertSame('', $html->getAttribute('class'));

        $body = $dom->querySelector('body');
        $body->classList->remove('a', 'd');
        $this->assertSame('b c', $body->getAttribute('class'));
    }

    /**
     * @group classList
     */
    public function testClassListToggle()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body class="  a   b c b a c"></body></html>');

        $html = $dom->querySelector('html');
        $isThere = $html->classList->toggle('a');
        $this->assertTrue($isThere);
        $this->assertSame('a', $html->getAttribute('class'));

        $body = $dom->querySelector('body');
        $isThere = $body->classList->toggle('a');
        $this->assertFalse($isThere);
        $this->assertSame('b c', $body->getAttribute('class'));

        $isThere = $body->classList->toggle('d');
        $this->assertTrue($isThere);
        $this->assertSame('b c d', $body->getAttribute('class'));
    }

    /**
     * @group classList
     */
    public function testClassListToggleForce()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body class="  a   b c b a c"></body></html>');

        $html = $dom->querySelector('html');
        $isThere = $html->classList->toggle('a', false);
        $this->assertFalse($isThere);
        $this->assertSame('', $html->getAttribute('class'));
        $isThere = $html->classList->toggle('a', true);
        $this->assertTrue($isThere);
        $this->assertSame('a', $html->getAttribute('class'));
        $isThere = $html->classList->toggle('a', true);
        $this->assertTrue($isThere);
        $this->assertSame('a', $html->getAttribute('class'));

        $body = $dom->querySelector('body');
        $isThere = $body->classList->toggle('a', false);
        $this->assertFalse($isThere);
        $this->assertSame('b c', $body->getAttribute('class'));
        $isThere = $body->classList->toggle('a', false);
        $this->assertFalse($isThere);
        $this->assertSame('b c', $body->getAttribute('class'));
        $isThere = $body->classList->toggle('b', true);
        $this->assertTrue($isThere);
        $this->assertSame('b c', $body->getAttribute('class'));
    }

    /**
     * @group classList
     */
    public function testClassListReplace()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body class="  a   b c b a c"></body></html>');

        $html = $dom->querySelector('html');
        $html->classList->replace('a', 'b');
        $this->assertSame('', $html->getAttribute('class'));

        $body = $dom->querySelector('body');
        $body->classList->replace('a', 'a');
        $this->assertSame('  a   b c b a c', $body->getAttribute('class')); // since no change is made

        $body->classList->replace('a', 'b');
        $this->assertSame('b c', $body->getAttribute('class'));

        $body->classList->replace('c', 'd');
        $this->assertSame('b d', $body->getAttribute('class'));
    }

    /**
     * @group classList
     */
    public function testClassListLength()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body class="  a   b c b a c"></body></html>');

        $html = $dom->querySelector('html');
        $this->assertSame(0, $html->classList->length);

        $body = $dom->querySelector('body');
        $this->assertSame(3, $body->classList->length);
    }

    /**
     * @group classList
     */
    public function testClassListValue()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body class="  a   b c b a c"></body></html>');

        $html = $dom->querySelector('html');
        $this->assertSame('', $html->classList->value);

        $body = $dom->querySelector('body');
        $this->assertSame('a b c', $body->classList->value);
    }

    /**
     * @group classList
     */
    public function testClassListUndefinedProperty()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body class="  a   b c b a c"></body></html>');

        $html = $dom->querySelector('html');
        try {
            $html->classList->someProperty;
            $this->assertTrue(false); // should not get here
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * @group classList
     */
    public function testClassListToString()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body class="  a   b c b a c"></body></html>');

        $html = $dom->querySelector('html');
        $this->assertSame('', (string) $html->classList);

        $body = $dom->querySelector('body');
        $this->assertSame('a b c', (string) $body->classList);
    }

    /**
     * @group classList
     */
    public function testClassListOverwrite()
    {
        $dom = new HTML5DOMDocument();
        $dom->loadHTML('<html><body class="a b c"></body></html>');

        $body = $dom->querySelector('body');
        $this->assertSame('a b c', (string) $body->classList);
        $this->assertSame('a b c', $body->getAttribute('class'));

        $body->setAttribute('class', 'd e f');
        $this->assertSame('d e f', (string) $body->classList);
        $this->assertSame('d e f', $body->getAttribute('class'));

        $body->classList = 'g h i';
        $this->assertSame('g h i', (string) $body->classList);
        $this->assertSame('g h i', $body->getAttribute('class'));
    }

    /**
     * 
     */
    public function testWrongCharsetMetaTag()
    {
        $html = '<!DOCTYPE html>' . "\n" . '<html><head><meta http-equiv="Content-Type" name="viewport" content="charset=UTF-8; width=device-width; initial-scale=1.0; text/html"></head><body>Hi</body></html>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($html);
        $resultHTML = $dom->saveHTML();
        $this->assertTrue($html === $resultHTML);
    }

    /**
     *
     */
    public function testLIBXML_HTML_NODEFDTD()
    {
        $content = '<div>hello</div>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($content, LIBXML_HTML_NODEFDTD);
        $expectedContent = '<html><body><div>hello</div></body></html>';
        $this->assertEquals($dom->saveHTML(), $expectedContent);
    }

    /**
     *
     */
    public function testLIBXML_HTML_NOIMPLIED()
    {

        $content = '<div>hello</div>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED);
        $this->assertEquals($dom->getElementsByTagName('html')->length, 0);
        $this->assertEquals($dom->getElementsByTagName('head')->length, 0);
        $this->assertEquals($dom->getElementsByTagName('body')->length, 0);
        $expectedContent = '<!DOCTYPE html>' . "\n" . '<div>hello</div>';
        $this->assertEquals($dom->saveHTML(), $expectedContent);
    }

    /**
     *
     */
    public function testCompatibilityWithDOMDocument()
    {

        $compareDOMs = function (HTML5DOMDocument $dom1, DOMDocument $dom2) {
            $this->assertEquals($dom1->getElementsByTagName('html')->length, $dom2->getElementsByTagName('html')->length);
            $this->assertEquals($dom1->getElementsByTagName('head')->length, $dom2->getElementsByTagName('head')->length);
            $this->assertEquals($dom1->getElementsByTagName('body')->length, $dom2->getElementsByTagName('body')->length);

            $updateNewLines = function (&$content) {
                $content = str_replace("\n<head>", '<head>', $content);
                $content = str_replace("\n<body>", '<body>', $content);
                $content = str_replace("\n</html>", '</html>', $content);
                $content = rtrim($content, "\n");
            };

            $result1 = $dom1->saveHTML();
            $result2 = $dom2->saveHTML();
            $result2 = preg_replace('/\<\!DOCTYPE(.*?)\>/', '<!DOCTYPE html>', $result2);
            $updateNewLines($result1);
            $updateNewLines($result2);
            $this->assertEquals($result1, $result2);

            if ($dom1->getElementsByTagName('html')->length > 0 && $dom2->getElementsByTagName('html')->length > 0) {
                $html1 = $dom1->saveHTML($dom1->getElementsByTagName('html')[0]);
                $html2 = $dom2->saveHTML($dom2->getElementsByTagName('html')[0]);
                $updateNewLines($html1);
                $updateNewLines($html2);
                $this->assertEquals($html1, $html2);
            }

            if ($dom1->getElementsByTagName('body')->length > 0 && $dom2->getElementsByTagName('body')->length > 0) {
                $body1 = $dom1->saveHTML($dom1->getElementsByTagName('body')[0]);
                $body2 = $dom2->saveHTML($dom2->getElementsByTagName('body')[0]);
                $this->assertEquals($body1, $body2);

                if ($dom1->getElementsByTagName('body')[0]->firstChild !== null) {
                    $firstChild1 = $dom1->saveHTML($dom1->getElementsByTagName('body')[0]->firstChild);
                    $firstChild2 = $dom2->saveHTML($dom2->getElementsByTagName('body')[0]->firstChild);
                    $this->assertEquals($firstChild1, $firstChild2);
                }
            }
        };

        $compareContent = function ($content) use ($compareDOMs) {
            $dom = new HTML5DOMDocument();
            $dom->loadHTML($content);
            $dom2 = new DOMDocument();
            $dom2->loadHTML($content);
            $compareDOMs($dom, $dom2);
        };

        $content = '<div>hello</div>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED);
        $dom2 = new DOMDocument();
        $dom2->loadHTML($content, LIBXML_HTML_NOIMPLIED);
        $compareDOMs($dom, $dom2);

        $content = '<div>hello</div>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($content, LIBXML_HTML_NODEFDTD);
        $dom2 = new DOMDocument();
        $dom2->loadHTML($content, LIBXML_HTML_NODEFDTD);
        $compareDOMs($dom, $dom2);

        $compareContent('<div>hello</div>');
        $compareContent('<body>hello</body>');
        $compareContent('<html><div>hello</div></html>');
        $compareContent('<html><head></head><body><div>hello</div></body></html>');
    }

    /**
     *
     */
    public function testDuplicateElementIDsQueries()
    {
        $content = '<div id="key1">1</div><div id="key1">2</div><div id="key1">3</div><div id="keyA">A</div>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($content, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
        $this->assertEquals($dom->getElementById('key1')->innerHTML, '1');
        $this->assertEquals($dom->querySelector('[id="key1"]')->innerHTML, '1');
        $this->assertEquals($dom->querySelectorAll('[id="key1"]')->length, 3);
        $this->assertEquals($dom->querySelectorAll('[id="key1"]')[0]->innerHTML, '1');
        $this->assertEquals($dom->querySelectorAll('[id="key1"]')[1]->innerHTML, '2');
        $this->assertEquals($dom->querySelectorAll('[id="key1"]')[2]->innerHTML, '3');
    }

    /**
     *
     */
    public function testDuplicateElementIDsException()
    {
        $content = '<div id="key1">1</div><div><div id="key1">2</div></div>';
        $dom = new HTML5DOMDocument();
        $this->expectException('\Exception');
        $dom->loadHTML($content);
    }

    /**
     *
     */
    public function testSpecialCharsInScriptTags()
    {
        $js1 = 'var f1=function(t){
            return t.replace(/</g,"&lt;").replace(/>/g,"&gt;");
        };';
        $js2 = 'var f2=function(t){
            return t.replace(/</g,"&lt;").replace(/>/g,"&gt;");
        };';
        $content = '<html><head><script src="url1"/><script src="url2"></script><script type="text/javascript">' . $js1 . '</script><script>' . $js2 . '</script></head></html>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($content);
        $scripts = $dom->querySelectorAll('script');
        $this->assertEquals($scripts[0]->innerHTML, '');
        $this->assertEquals($scripts[1]->innerHTML, '');
        $this->assertEquals($scripts[2]->innerHTML, $js1);
        $this->assertEquals($scripts[3]->innerHTML, $js2);
        $this->assertEquals($dom->saveHTML(), "<!DOCTYPE html>\n" . str_replace('<script src="url1"/>', '<script src="url1"></script>', $content));
    }

    /**
     *
     */
    public function testFragments()
    {
        $fragments = [
            '<div>text</div>',
            '<p>text</p>',
            '<script type="text/javascript">var a = 1;</script>',
        ];
        foreach ($fragments as $fragment) {
            $dom = new HTML5DOMDocument();
            $dom->loadHTML($fragment, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $this->assertEquals($dom->querySelectorAll('*')->length, 1);
            $this->assertEquals($fragment, $dom->saveHTML());
        }
    }

    /**
     *
     */
    public function testScriptsCDATA()
    {
        // There was a bug when "html5-dom-document-internal-cdata" is used and there is no html tag.
        $html = '<script type="text/template"><div>Hi</div></script>';
        $expectedResult = '<!DOCTYPE html>' . "\n" . '<html><body><script type="text/template"><div>Hi</div></script></body></html>';
        $dom = new \IvoPetkov\HTML5DOMDocument();
        $dom->loadHTML($html);
        $this->assertEquals($expectedResult, $dom->saveHTML());
    }

    /**
     * 
     * @return array
     */
    public function propertyGetterTestDataProvider()
    {
        return [
            [
                '<html><body><p><span>Lorem Ipsum</span> &mdash; <span>dolor sit amet,</span></p></body></html>',
                'Lorem Ipsum html5-dom-document-internal-entity1-mdash-end dolor sit amet,',
                'Lorem Ipsum — dolor sit amet,'
            ]
        ];
    }

    /**
     * @dataProvider propertyGetterTestDataProvider
     */
    public function testInternalEntityFromGetters(string $dom, string $expectedFromProperty, string $expectedFromGetter)
    {
        $domDoc = new HTML5DOMDocument('1.0', 'utf-8');
        $domDoc->loadHTML($dom);
        $xpath = new DOMXPath($domDoc);

        $xPathNodeList = $xpath->query('//p');

        foreach ($xPathNodeList as $node) {
            static::assertInstanceOf(HTML5DOMElement::class, $node);
            static::assertEquals($expectedFromProperty, $node->nodeValue);
            static::assertEquals($expectedFromGetter, $node->getNodeValue());

            static::assertEquals($expectedFromProperty, $node->textContent);
            static::assertEquals($expectedFromGetter, $node->getTextContent());
        }

        $querySelectorNodeList = $domDoc->querySelectorAll('p');

        foreach ($querySelectorNodeList as $node) {
            static::assertInstanceOf(HTML5DOMElement::class, $node);
            static::assertEquals($expectedFromProperty, $node->nodeValue);
            static::assertEquals($expectedFromGetter, $node->getNodeValue());

            static::assertEquals($expectedFromProperty, $node->textContent);
            static::assertEquals($expectedFromGetter, $node->getTextContent());
        }
    }

    /**
     *
     */
    public function testSaveHTMLWithoutLoadHTML()
    {
        $dom = new \IvoPetkov\HTML5DOMDocument();
        $dom->appendChild($dom->createElement('div'));
        $dom->querySelector('*')->innerHTML = 'text';
        $this->assertEquals('<div>text</div>', $dom->saveHTML());
    }

    /**
     *
     */
    public function testAllowDuplicateIDsWhenModifyingElements()
    {
        $dom = new \IvoPetkov\HTML5DOMDocument();
        $dom->loadHTML('<html><body><div id="id1"></div><span id="id1"></span></body></div>', HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
        $body = $dom->querySelector('body');
        $body->innerHTML .= '<strong></strong>';
        $strong = $dom->querySelector('strong');
        $strong->outerHTML = '<strong>text</strong>';
        $expectedResult = '<!DOCTYPE html>' . "\n" . '<html><body><div id="id1"></div><span id="id1"></span><strong>text</strong></body></html>';
        $this->assertEquals($expectedResult, $dom->saveHTML());
    }
}
