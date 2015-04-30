<?php

namespace timesplinter\tsfw\template\test;

use timesplinter\tsfw\template\common\TemplateCacheStrategy;
use timesplinter\tsfw\template\common\TemplateEngine;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015, TiMESPLiNTER Webdevelopment
 */
abstract class AbstractTemplateTest extends \PHPUnit_Framework_TestCase
{
	/** @var TemplateEngine */
	protected $tplEngine;
	
	protected function setUp()
	{
		/** @var TemplateCacheStrategy $cacheStrategyMock */
		$this->tplEngine = new TemplateEngine();
	}
}

/* EOF */