<?php

namespace timesplinter\tsfw\customtags;

use timesplinter\tsfw\htmlparser\ElementNode;
use timesplinter\tsfw\htmlparser\TextNode;
use timesplinter\tsfw\template\TagInline;
use timesplinter\tsfw\template\TagNode;
use timesplinter\tsfw\template\TemplateEngine;
use timesplinter\tsfw\template\TemplateTag;

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
		
		if($data instanceof \DateTime)
			return $data->format('Y-m-d H:i:s');
		elseif(is_scalar($data) === false)
			return print_r($data, true);
		
		return $data;
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