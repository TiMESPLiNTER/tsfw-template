<?php

namespace timesplinter\tsfw\htmlparser;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
class DocumentTypeNode extends HtmlNode
{
	public function __construct(HtmlDoc $htmlDocument)
	{
		parent::__construct(HtmlNode::DOCUMENT_TYPE_NODE, $htmlDocument);
	}
}

/* EOF */