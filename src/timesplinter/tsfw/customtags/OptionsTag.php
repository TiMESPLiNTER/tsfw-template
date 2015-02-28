<?php

namespace timesplinter\tsfw\customtags;

use timesplinter\tsfw\htmlparser\ElementNode;
use timesplinter\tsfw\htmlparser\TextNode;
use timesplinter\tsfw\template\TagNode;
use timesplinter\tsfw\template\TemplateEngine;
use timesplinter\tsfw\template\TemplateTag;

/**
 *
 *
 * @author Pascal Muenst <entwicklung@metanet.ch>
 * @copyright Copyright (c) 2012, METANET AG, www.metanet.ch
 * @version 1.0.0
 */
class OptionsTag extends TemplateTag implements TagNode
{
	public function replaceNode(TemplateEngine $tplEngine, ElementNode $node)
	{
		$tplEngine->checkRequiredAttrs($node, array('options'));

		// DATA
		$compareValue = ($node->getAttribute('selected')->value !== null)?$node->getAttribute('selected')->value:null;

		$dataKey = $node->getAttribute('options')->value;

		$compareStr = '$selected = null;';

		if($compareValue !== null) {
			//$selVal = $tplEngine->getSelectorValue($node->getAttribute('selected')->value);
			$compareStr = '$compVal = $this->getDataFromSelector(\'' . $compareValue . '\');';
			$compareStr .= '$selected = ((is_array($compVal) && in_array($key, $compVal)) || (is_string($compVal) && $key == $compVal))?\' selected\':null;';
		}

		$textContent = '<?php foreach($this->getDataFromSelector(\'' . $dataKey . '\') as $key => $val) {
			' . $compareStr . '
			echo \'<option value="\'.$key.\'"\'.$selected.\'>\'.$val.\'</option>\' . "\n";
		} ?>';

		$newNode = new TextNode($tplEngine->getDomReader());
		$newNode->content = $textContent;

		$node->parentNode->insertBefore($newNode, $node);
		$node->parentNode->removeNode($node);
	}

	/**
	 * @return string
	 */
	public static function getName()
	{
		return 'options';
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