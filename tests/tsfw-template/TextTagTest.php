<?php

namespace timesplinter\test;

use timesplinter\tsfw\template\test\AbstractTemplateTest;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015, TiMESPLiNTER Webdevelopment
 */
class TextTagTest extends AbstractTemplateTest
{
	public function testTag()
	{
		file_put_contents($this->testInputFile, '<tst:text value="test" />');
		
		$this->assertEquals('foobar', $this->tplEngine->getResultAsHtml($this->testInputFile, array(
			'test' => 'foobar'
		)));
	}

	public function testTagSelfClosing()
	{
		file_put_contents($this->testInputFile, '<p><tst:text value="test"></p>');

		$this->assertEquals('<p>foobar</p>', $this->tplEngine->getResultAsHtml($this->testInputFile, array(
			'test' => 'foobar'
		)));
	}

	public function testTagInline()
	{
		file_put_contents($this->testInputFile, '<img src="img.jpg" alt="{tst:text value=\'test\'}">');

		$this->assertEquals('<img src="img.jpg" alt="foobar">', $this->tplEngine->getResultAsHtml($this->testInputFile, array(
			'test' => 'foobar'
		)));
	}
}

/* EOF */