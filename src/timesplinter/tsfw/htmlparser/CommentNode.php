<?php

namespace timesplinter\tsfw\htmlparser;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
class CommentNode extends HtmlNode
{
	public function __construct(HtmlDoc $htmlDocument)
	{
		parent::__construct(HtmlNode::COMMENT_NODE, $htmlDocument);
	}
}

/* EOF */