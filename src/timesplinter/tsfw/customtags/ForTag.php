<?php

namespace timesplinter\tsfw\customtags;

use timesplinter\tsfw\htmlparser\ElementNode;
use timesplinter\tsfw\htmlparser\TextNode;
use timesplinter\tsfw\template\TagNode;
use timesplinter\tsfw\template\TemplateEngine;
use timesplinter\tsfw\template\TemplateEngineException;
use timesplinter\tsfw\template\TemplateTag;

/**
 * @author Pascal Münst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
class ForTag extends TemplateTag implements TagNode
{
	public function replaceNode(TemplateEngine $tplEngine, ElementNode $node)
	{
		// DATA
		$tplEngine->checkRequiredAttrs($node, array('var', 'as'));

		$dataKeyAttr = $node->getAttribute('var')->value;
		$asVarAttr = $node->getAttribute('as')->value;
		
		$keyVarAttr = $node->getAttribute('key')->value;
		$counterAttr = $node->getAttribute('counter')->value;

		$oddEvenAttr = $node->getAttribute('odd-even')->value;
		$firstLastAttr = $node->getAttribute('first-last')->value;
		
		$stepIncrement = $node->getAttribute('step')->value;
		$grabCount = $node->getAttribute('grab')->value;
		
		if($stepIncrement === null && $grabCount !== null)
			$stepIncrement = $grabCount;
		elseif($stepIncrement === null)
			$stepIncrement = 1;
		
		if($stepIncrement == 0)
			throw new TemplateEngineException('Use a step value other than 0. This will end up in an endless loop');

		$for_i = '$for_i_' . $asVarAttr;
		$for_key = '$for_key_' . $asVarAttr;
		$for_val = '$for_val_' . $asVarAttr;
		$for_data = '$for_data_' . $asVarAttr;
		$for_data_count = '$for_data_count_' . $asVarAttr;

		$phpCode = '<?php
			' . $for_data . ' = $this->getDataFromSelector(\'' . $dataKeyAttr . '\');
			' . $for_data_count . ' = count(' . $for_data . ');
			' . $for_i . ' = 0;
			
			';
		
		if($tplEngine->isFollowedBy($node, 'else') === true) {
			$phpCode .= 'if(' . $for_data_count . ' > 0):
			';
		}

		$phpCode .= 'for(' . $for_val . ' = current(' . $for_data . '), ' . $for_key . ' = key(' . $for_data . '); ' . $for_val . ';  ' . $for_val . ' = next(' . $for_data . '), ' . $for_key . ' = key(' . $for_data . '), ' . $for_i . ' += ' . $stepIncrement . '):
			' . (($counterAttr !== null)?'$this->addData(\'' . $counterAttr . '\', ' . $for_i . ', true);':null) . '	
			' . (($keyVarAttr !== null)?'$this->addData(\'' . $keyVarAttr . '\', ' . $for_key . ', true);':null);
				
		if($grabCount === null || $grabCount == 1) {
			$phpCode .= '$this->addData(\'' . $asVarAttr . '\', ' . $for_val . ', true);';		
		} else {
			$phpCode .= '$tmpGrabGroup = array(
				key(' . $for_data . ') => current(' . $for_data . ')
			);
			
			for($i = 2; $i <= ' . $grabCount. '; ++$i) {
				if(($tmpNextEntry = next(' . $for_data . ')) === false)
					break;
					
				$tmpGrabGroup[key(' . $for_data . ')] = $tmpNextEntry;
			}
			
			$this->addData(\'' . $asVarAttr . '\', $tmpGrabGroup, true);';
		}

		$phpCode .= (($oddEvenAttr !== null)?'$this->addData(\'' . $oddEvenAttr . '\', (((' . $for_i . '/' . $stepIncrement .')%2 === 0)?\'odd\':\'even\'), true);':null) . '	
			' . (($firstLastAttr !== null)?'$this->addData(\'' . $firstLastAttr . '\', ((' . $for_i . ' === 0)?\'first\':(((' . $for_i . '/' . $stepIncrement . ') === ' . $for_data_count . '-1)?\'last\':null)), true);':null) . '	
		?>
		' . $node->getInnerHtml() . '
		<?php endfor; ?>';

		$newNode = new TextNode($tplEngine->getDomReader());
		$newNode->content = $phpCode;
		
		$node->parentNode->replaceNode($node, $newNode);
	}

	/**
	 * @return string
	 */
	public static function getName()
	{
		return 'for';
	}

	/**
	 * @return bool
	 */
	public static function isElseCompatible()
	{
		return true;
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
