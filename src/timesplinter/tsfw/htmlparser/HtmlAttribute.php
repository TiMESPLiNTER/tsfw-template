<?php

namespace timesplinter\tsfw\htmlparser;

/**
 * @author Pascal Münst <dev@timesplinter.ch>
 * @copyright	Copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
class HtmlAttribute
{
	public $key;
	public $value;

	public function __construct($key, $value)
	{
		$this->key = $key;
		$this->value = $value;
	}
}

/* EOF */