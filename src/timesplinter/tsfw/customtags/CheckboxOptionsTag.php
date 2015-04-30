<?php

namespace timesplinter\tsfw\customtags;

use timesplinter\tsfw\htmlparser\ElementNode;
use timesplinter\tsfw\htmlparser\TextNode;
use timesplinter\tsfw\template\TagNode;
use timesplinter\tsfw\template\TemplateEngine;
use timesplinter\tsfw\template\TemplateTag;

/**
 * @author Pascal Münst
 * @copyright Copyright (c) 2012, Pascal Münst
 */
class CheckboxOptionsTag extends TemplateTag implements TagNode
{
	public function replaceNode(TemplateEngine $tplEngine, ElementNode $node)
	{
		// DATA
		$tplEngine->checkRequiredAttrs($node, array('options', 'checked'));

		$checkedSelector = $node->getAttribute('checked')->value;
		$optionsSelector = $node->getAttribute('options')->value;
		$fldName = $node->getAttribute('name')->value . '[]';

		$textContent = "<?php print " . __CLASS__ . "::render(\$this, '{$fldName}', '{$optionsSelector}', '{$checkedSelector}'); ?>";
		
		$newNode = new TextNode($tplEngine->getDomReader());
		$newNode->content = $textContent;

		$node->parentNode->insertBefore($newNode, $node);
		$node->parentNode->removeNode($node);
	}
	
	public static function render(TemplateEngine $tplEngine, $fldName, $optionsSelector, $checkedSelector)
	{
		$options = $tplEngine->getDataFromSelector($optionsSelector);
		$selection = (array)$tplEngine->getDataFromSelector($checkedSelector);
		
		$html = '<ul>';  
		
		foreach($options as $key => $val) {
			$checked = in_array($key, $selection) ? ' checked' : null;
			$html .= '<li><label><input type="checkbox" value="' . $key . '" name="' . $fldName . '"' . $checked . '> ' . $val . '</label></li>' . "\n";
		}
		
		$html .= '</ul>';
		
		return $html;
	}

	/**
	 * @return string
	 */
	public static function getName()
	{
		return 'checkboxOptions';
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