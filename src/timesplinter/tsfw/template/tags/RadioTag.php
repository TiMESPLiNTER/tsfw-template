<?php

namespace timesplinter\tsfw\template\tags;

use timesplinter\tsfw\htmlparser\ElementNode;
use timesplinter\tsfw\htmlparser\HtmlAttribute;
use timesplinter\tsfw\template\common\TagNode;
use timesplinter\tsfw\template\common\TemplateEngine;
use timesplinter\tsfw\template\common\TemplateTag;

/**
 * @author Pascal Münst <dev@timesplinter.ch>
 * @copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
class RadioTag extends TemplateTag implements TagNode
{
	public function replaceNode(TemplateEngine $tplEngine, ElementNode $node)
	{
		// DATA
		$sels = $node->getAttribute('selection')->value;
		$selsStr = '$this->getDataFromSelector(\'' . $sels . '\')';
		$value = $node->getAttribute('value')->value;
		$node->removeAttribute('selection');
		
		$node->namespace = null;
		$node->tagName = 'input';
		if($sels !== null)
			$node->tagExtension = " <?php echo ((is_array({$selsStr}) && in_array({$value}, {$selsStr})) || ({$selsStr} == '{$value}'))?' checked':null; ?>";

		$node->addAttribute(new HtmlAttribute('type', 'radio'));
	}

	/**
	 * @return string
	 */
	public static function getName()
	{
		return 'radio';
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