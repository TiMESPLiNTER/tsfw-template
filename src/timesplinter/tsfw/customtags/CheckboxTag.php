<?php

namespace timesplinter\tsfw\customtags;

use timesplinter\tsfw\htmlparser\ElementNode;
use timesplinter\tsfw\htmlparser\HtmlAttribute;
use timesplinter\tsfw\template\TagNode;
use timesplinter\tsfw\template\TemplateEngine;
use timesplinter\tsfw\template\TemplateTag;

/**
 * @author Pascal Muenst <entwicklung@metanet.ch>
 * @copyright (c) 2012, METANET AG
 * @version 1.0.0
 */
class CheckboxTag extends TemplateTag implements TagNode
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

		$node->addAttribute(new HtmlAttribute('type', 'checkbox'));
	}

	/**
	 * @return string
	 */
	public static function getName()
	{
		return 'checkbox';
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