<?php

namespace timesplinter\tsfw\htmlparser;

/**
 * @author Pascal Münst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
class DocumentNode extends HtmlNode
{
	public $childNodes;

	public function __construct(HtmlDoc $htmlDocument)
	{
		parent::__construct(HtmlNode::DOCUMENT_NODE, $htmlDocument);

		$this->childNodes = array();
	}
}

/* EOF */