<?php

namespace timesplinter\tsfw\template\test;

use timesplinter\tsfw\template\NullTemplateCache;
use timesplinter\tsfw\template\TemplateCacheStrategy;
use timesplinter\tsfw\template\TemplateEngine;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015, TiMESPLiNTER Webdevelopment
 */
abstract class AbstractTemplateTest extends \PHPUnit_Framework_TestCase
{
	/** @var TemplateEngine */
	protected $tplEngine;
	protected $testInputFile;
	
	protected function setUp()
	{
		/** @var TemplateCacheStrategy $cacheStrategyMock */
		$this->tplEngine = new TemplateEngine(
			new NullTemplateCache(), 'tst'
		);

		$this->testInputFile = tempnam(sys_get_temp_dir(), 'tpltest');
	}
}

/* EOF */