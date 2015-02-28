<?php

namespace timesplinter\tsfw\htmlparser;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER Webedevlopment
 */
class TextNode extends HtmlNode
{
	public function __construct(HtmlDoc $htmlDocument)
	{
		parent::__construct(HtmlNode::TEXT_NODE, $htmlDocument);
	}
}

/* EOF */