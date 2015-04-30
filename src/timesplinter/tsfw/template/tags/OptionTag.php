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
class OptionTag extends TemplateTag implements TagNode
{
	public function replaceNode(TemplateEngine $tplEngine, ElementNode $node)
	{
		// DATA
		$sels = $node->getAttribute('selection')->value;
		$valueAttr = $node->getAttribute('value')->value;
		$value = is_numeric($valueAttr)?$valueAttr:"'" . $valueAttr . "'";
		$type = $node->getAttribute('type')->value;
		$node->removeAttribute('selection');
		
		$node->namespace = null;
		$node->tagName = 'input';
		
		if($sels !== null)
			$node->tagExtension = " <?php echo in_array({$value}, \$this->getData('{$sels}'))?' checked=\"checked\"':null; ?>";
		
		$node->addAttribute(new HtmlAttribute('type', $type));
	}

	/**
	 * @return string
	 */
	public static function getName()
	{
		return 'option';
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