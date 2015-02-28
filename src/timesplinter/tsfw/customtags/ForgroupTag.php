<?php

namespace timesplinter\tsfw\customtags;

use timesplinter\tsfw\htmlparser\ElementNode;
use timesplinter\tsfw\htmlparser\TextNode;
use timesplinter\tsfw\template\TagNode;
use timesplinter\tsfw\template\TemplateEngine;
use timesplinter\tsfw\template\TemplateEngineException;
use timesplinter\tsfw\template\TemplateTag;

class ForgroupTag extends TemplateTag implements TagNode
{
	private $var;
	private $no;
	
	public function replaceNode(TemplateEngine $tplEngine, ElementNode $node)
	{
		$var = $node->getAttribute('var')->value;
		
		$entryNoArr = explode(':', $var);
		$this->no = $entryNoArr[0];
		$this->var = $entryNoArr[1];
		
		$tplEngine->checkRequiredAttrs($node, array('var'));
		
		$replNode = new TextNode($tplEngine->getDomReader());

		$varName = $this->var . $this->no;

		$replNode->content = "<?php \$tmpGrpVal = \$this->getDataFromSelector('{$varName}', true);\n";
		$replNode->content .= " if(\$tmpGrpVal !== null) {\n";
		$replNode->content .= "\$this->addData('{$this->var}', \$tmpGrpVal, true); ?>";
		$replNode->content .= self::prepareHtml($node->getInnerHtml());
		$replNode->content .= "<?php } ?>";
		
		$node->getParentNode()->replaceNode($node, $replNode);
	}
	
	private function prepareHtml($html)
	{
		$newHtml = preg_replace_callback('/\{' . $this->var . '\.(.*?)\}/', array($this,'replace'), $html);
		$newHtmlRepl = preg_replace_callback('/\{(\w+?)(?:\.([\w|\.]+))?\}/', array($this,'replaceForeign'), $newHtml);
		
		return $newHtmlRepl;	
	}
	
	private function replaceForeign($matches)
	{
		return '<?php echo $' . $matches[1] . '->' . str_replace('.', '->', $matches[2]) . '; ?>';
	}
	
	private function replace($matches)
	{
		return '<?php echo $' . $this->var . $this->no . '->' . str_replace('.', '->', $matches[1]) . '; ?>';
	}

	/**
	 * @return string
	 */
	public static function getName()
	{
		return 'forgroup';
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