<?php

namespace timesplinter\tsfw\template;

use Exception;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015 by TiMESPLiNTER Webdevelopment
 */
class TemplateParseException extends TemplateEngineException
{
	protected $tplFile;
	protected $tplLine;

	public function __construct($message = '', $code = 0, $tplFile, $tplLine, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);

		$this->file = $tplFile;
		$this->line = $tplLine;
	}

	/**
	 * @return string
	 */
	public function getTplFile()
	{
		return $this->file;
	}

	/**
	 * @return string
	 */
	public function getTplLine()
	{
		return $this->line;
	}
}