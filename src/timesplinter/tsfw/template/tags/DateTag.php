<?php

namespace timesplinter\tsfw\template\tags;

use timesplinter\tsfw\htmlparser\ElementNode;
use timesplinter\tsfw\htmlparser\TextNode;
use timesplinter\tsfw\template\common\TagNode;
use timesplinter\tsfw\template\common\TemplateEngine;
use timesplinter\tsfw\template\common\TemplateTag;

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