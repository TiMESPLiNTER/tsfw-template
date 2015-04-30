<?php

namespace timesplinter\tsfw\template\tags;

use timesplinter\tsfw\htmlparser\ElementNode;
use timesplinter\tsfw\htmlparser\TextNode;
use timesplinter\tsfw\template\common\TagInline;
use timesplinter\tsfw\template\common\TagNode;
use timesplinter\tsfw\template\common\TemplateEngine;
use timesplinter\tsfw\template\common\TemplateTag;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
class PrintTag extends TemplateTag implements TagNode, TagInline
{
	public function replaceNode(TemplateEngine $tplEngine, ElementNode $node)
	{
		$replValue = $this->replace($tplEngine, $node->getAttribute('var')->value);

		$replNode = new TextNode($tplEngine->getDomReader());
		$replNode->content = $replValue;

		$node->parentNode->replaceNode($node, $replNode);
	}

	public function replaceInline(TemplateEngine $tplEngine, $params)
	{
		return $this->replace($tplEngine, $params['var']);
	}

	public function replace(TemplateEngine $tplEngine, $selector)
	{
		return '<?php echo ' . __CLASS__ .'::generateOutput($this, \'' . $selector . '\'); ?>';
	}
	
	public static function generateOutput(TemplateEngine $templateEngine, $selector)
	{
		$data = $templateEngine->getDataFromSelector($selector);
		
		if(is_array($data) === false && (is_object($data) === false || is_callable(array($data, '__toString')) === true))
			return $data;
		
		return print_r($data, true);
	}

	/**
	 * @return string
	 */
	public static function getName()
	{
		return 'print';
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