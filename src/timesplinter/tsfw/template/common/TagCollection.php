<?php

namespace timesplinter\tsfw\template\common;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015, TiMESPLiNTER Webdevelopment
 */
interface TagCollection
{
	/**
	 * @return string
	 */
	public function getPrefix();

	/**
	 * @return string[]
	 */
	public function getAvailableTags();
}

/* EOF */