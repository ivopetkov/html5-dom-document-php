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
class Test extends PHPUnit_Framework_TestCase
{

    /**
     * 
     */
    public function testSaveHTML()
    {
        $bodyContent = '<div>hello</div>';

        $testSource = function($source, $expectedSource) {
            $dom = new HTML5DOMDocument();
            $dom->loadHTML($source);
            $this->assertTrue($expectedSource === $dom->saveHTML());
        };

        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>' . $bodyContent . '</body></html>';
        $testSource('<!DOCTYPE html><html><body>' . $bodyContent . '</body></html>', $expectedSource);
        $testSource('<html><body>' . $bodyContent . '</body></html>', $expectedSource);
        $testSource('<body>' . $bodyContent . '</body>', $expectedSource);
        $testSource($bodyContent, $expectedSource);

        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><head></head><body>' . $bodyContent . '</body></html>';
        $testSource('<!DOCTYPE html><html><head></head><body>' . $bodyContent . '</body></html>', $expectedSource);
        $testSource('<html><head></head><body>' . $bodyContent . '</body></html>', $expectedSource);
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
                . '<div>text1 ' . "\n" . 'text2 </div>';
        $expectedSource = '<!DOCTYPE html>' . "\n" . '<html><body>' . $bodyContent . '</body></html>';
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($bodyContent);
        $this->assertTrue($expectedSource === $dom->saveHTML());
    }

    /**
     * 
     */
    public function testInserHTML()
    {
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
    }

}
