<?php

namespace timesplinter\tsfw\template\tags;

use timesplinter\tsfw\htmlparser\ElementNode;
use timesplinter\tsfw\htmlparser\TextNode;
use timesplinter\tsfw\template\common\TagNode;
use timesplinter\tsfw\template\common\TemplateEngine;
use timesplinter\tsfw\template\common\TemplateEngineException;
use timesplinter\tsfw\template\common\TemplateTag;

/**
 * @author Pascal Münst <entwicklung@metanet.ch>
 * @copyright Copyright (c) 2012, METANET AG, www.metanet.ch
 */
class ElseTag extends TemplateTag implements TagNode
{
	public function replaceNode(TemplateEngine $tplEngine, ElementNode $tagNode)
	{
		$lastTplTag = $tplEngine->getLastTplTag();

		if($lastTplTag === null)
			throw new TemplateEngineException('There is no custom tag that can be followed by an ElseTag');

		/*if($lastTplTag->isElseable() === false)
			throw new TemplateEngineException('The custom tag "' . get_class($lastTplTag) . '" can not be followed by an ElseTag');*/

		$phpCode = '<?php else: ?>';
		$phpCode .= $tagNode->getInnerHtml();
		$phpCode .= '<?php endif; ?>';

		$textNode = new TextNode($tplEngine->getDomReader());
		$textNode->content = $phpCode;

		$tagNode->parentNode->replaceNode($tagNode, $textNode);

		$tagNode->parentNode->removeNode($tagNode);
	}

	/**
	 * @return string
	 */
	public static function getName()
	{
		return 'else';
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
		return false;
	}
}

/* EOF */