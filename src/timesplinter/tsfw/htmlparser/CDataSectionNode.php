<?php

namespace timesplinter\tsfw\htmlparser;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
class CDataSectionNode extends HtmlNode
{
	public function __construct(HtmlDoc $htmlDocument)
	{
		parent::__construct(HtmlNode::CDATA_SECTION_NODE, $htmlDocument);
	}
}

/* EOF */