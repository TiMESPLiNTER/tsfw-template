<?php

namespace timesplinter\tsfw\template\test;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015, TiMESPLiNTER Webdevelopment
 */
class ArrayToString
{
	protected $array;
	
	public function __construct(array $array)
	{
		$this->array = $array;
	}
	
	public function __toString()
	{
		return implode(',', $this->array);
	}
}

/* EOF */