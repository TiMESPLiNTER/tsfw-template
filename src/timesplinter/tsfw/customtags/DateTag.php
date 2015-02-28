<?php

namespace timesplinter\tsfw\customtags;

use timesplinter\tsfw\htmlparser\ElementNode;
use timesplinter\tsfw\htmlparser\TextNode;
use timesplinter\tsfw\template\TagNode;
use timesplinter\tsfw\template\TemplateEngine;
use timesplinter\tsfw\template\TemplateTag;

class DateTag extends TemplateTag implements TagNode
{
	public function replaceNode(TemplateEngine $tplEngine, ElementNode $tagNode)
	{
		$format = $tagNode->getAttribute('format')->value;
		$replNode = new TextNode($tplEngine->getDomReader());
		$replNode->content = '<?php echo date(\'' . $format . '\'); ?>';

		$tagNode->parentNode->replaceNode($tagNode, $replNode);
	}

	/**
	 * @return string
	 */
	public static function getName()
	{
		return 'date';
	}

	/**
	 * @return bool
	 */
	public static function isElseCompatible()
	{
		return false;
	}

	/**
	 * @return bool
	 */
	public static function isSelfClosing()
	{
		return true;
	}
}

/* EOF */