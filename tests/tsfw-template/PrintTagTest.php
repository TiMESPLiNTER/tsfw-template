<?php

namespace timesplinter\test;

use timesplinter\tsfw\template\test\AbstractTemplateTest;
use timesplinter\tsfw\template\test\ArrayToString;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015, TiMESPLiNTER Webdevelopment
 */
class TextTagTest extends AbstractTemplateTest
{
	public function testTag()
	{
		$this->assertEquals('foobar', $this->tplEngine->render('<tst.print var="test" />', array(
			'test' => 'foobar'
		)));
	}

	public function testTagSelfClosing()
	{
		$this->assertEquals('<p>foobar</p>', $this->tplEngine->render('<p><tst.print var="test"></p>', array(
			'test' => 'foobar'
		)));
	}
	
	public function testToString()
	{
		$this->assertEquals('foo,bar,baz', $this->tplEngine->render('<tst.print var="test" />', array(
			'test' => new ArrayToString(array('foo','bar','baz'))
		)));
	}
	
	public function testPrintf()
	{
		$this->assertEquals('Array
(
    [0] => foo
    [1] => bar
    [2] => baz
)
', $this->tplEngine->render('<tst.print var="test" />', array(
			'test' => array('foo','bar','baz')
		)));
	}

	public function testTagInline()
	{
		$this->assertEquals('<img src="img.jpg" alt="foobar">', $this->tplEngine->render('<img src="img.jpg" alt="{tst.print var=\'test\'}">', array(
			'test' => 'foobar'
		)));
	}
}

/* EOF */