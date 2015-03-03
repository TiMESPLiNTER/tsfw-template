<?php

namespace timesplinter\test;

use timesplinter\tsfw\template\NullTemplateCache;
use timesplinter\tsfw\template\TemplateCacheStrategy;
use timesplinter\tsfw\template\TemplateEngine;
use timesplinter\tsfw\template\test\AbstractTemplateTest;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015, TiMESPLiNTER Webdevelopment
 */
class TemplateEngineTest extends AbstractTemplateTest
{
	/** @var TemplateEngine */
	protected $tplEngine;
	
	protected function setUp()
	{
		/** @var TemplateCacheStrategy $cacheStrategyMock */
		$this->tplEngine = new TemplateEngine(
			new NullTemplateCache(), 'tst'
		);
	}
	
	public function testParseBasicTemplate()
	{
		$this->assertStringEqualsFile('templates/plain.html', $this->tplEngine->getResultAsHtml('templates/plain.html'));
	}
	
	public function testParseCustomTagTemplate()
	{
		$this->assertStringEqualsFile('templates-result/custom-tags.html', $this->tplEngine->getResultAsHtml('templates/custom-tags.html', array(
			'title' => 'Foo',
			'content' => 'Bar baz'
		)));
	}
}

/* EOF */