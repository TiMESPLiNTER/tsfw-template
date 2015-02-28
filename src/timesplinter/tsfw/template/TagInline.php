<?php

namespace timesplinter\tsfw\template;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
interface TagInline
{
	/**
	 * Replace the inline tag
	 *
	 * @param TemplateEngine $tplEngine
	 * @param string $tagStr
	 */
	public function replaceInline(TemplateEngine $tplEngine, $tagStr);

	/**
	 * @return string
	 */
	public static function getName();
}

/* EOF */