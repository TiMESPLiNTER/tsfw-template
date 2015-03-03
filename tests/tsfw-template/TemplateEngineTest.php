<?php

namespace timesplinter\test;

use timesplinter\tsfw\template\test\AbstractTemplateTest;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015, TiMESPLiNTER Webdevelopment
 */
class TemplateEngineTest extends AbstractTemplateTest
{
	protected $basePath;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->basePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
	}
	
	public function testParseBasicTemplate()
	{
		
		
		$this->assertStringEqualsFile($this->basePath . 'templates/plain.html', $this->tplEngine->getResultAsHtml($this->basePath . 'templates/plain.html'));
	}
	
	public function testParseCustomTagTemplate()
	{
		$this->assertStringEqualsFile($this->basePath . 'templates-result/custom-tags.html', $this->tplEngine->getResultAsHtml($this->basePath . 'templates/custom-tags.html', array(
			'title' => 'Foo',
			'content' => 'Bar baz'
		)));
	}
}

/* EOF */