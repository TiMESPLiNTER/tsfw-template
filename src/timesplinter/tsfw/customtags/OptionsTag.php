<?php

namespace timesplinter\tsfw\customtags;

use timesplinter\tsfw\htmlparser\ElementNode;
use timesplinter\tsfw\htmlparser\TextNode;
use timesplinter\tsfw\template\TagNode;
use timesplinter\tsfw\template\TemplateEngine;
use timesplinter\tsfw\template\TemplateTag;

/**
 * @author Pascal Muenst <entwicklung@metanet.ch>
 * @copyright Copyright (c) 2012, METANET AG, www.metanet.ch
 */
class OptionsTag extends TemplateTag implements TagNode
{
	public function replaceNode(TemplateEngine $tplEngine, ElementNode $node)
	{
		$tplEngine->checkRequiredAttrs($node, array('options'));

		// DATA
		$selectionSelector = ($node->getAttribute('selected') !== null) ? "'{$node->getAttribute('selected')->value}'" : null;
		$optionsSelector = "'{$node->getAttribute('options')->value}'";

		$textContent = '<?php echo ' . __CLASS__ . '::render($this, ' . $optionsSelector . ', ' . $selectionSelector . '); ?>';

		$newNode = new TextNode($tplEngine->getDomReader());
		$newNode->content = $textContent;

		$node->parentNode->insertBefore($newNode, $node);
		$node->parentNode->removeNode($node);
	}

	public static function render(TemplateEngine $tplEngine, $optionsSelector, $selectedSelector)
	{
		$options = $tplEngine->getDataFromSelector($optionsSelector);
		$selection = array();

		if($selectedSelector !== null)
			$selection = (array)$tplEngine->getDataFromSelector($selectedSelector);

		return self::renderOptions($options, $selection);
	}

	public static function renderOptions(array $options, array $selection)
	{
		$html = '';

		foreach($options as $key => $value) {
			if(is_array($value) === true) {
				$html .= '<optgroup label="' . $key . '">' . PHP_EOL . self::renderOptions($value, $selection) . '</optgroup>' . PHP_EOL;
			} else {
				$selected = in_array($key, $selection) ? ' selected' : null;
				$html .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>' . PHP_EOL;
			}
		}

		return $html;
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