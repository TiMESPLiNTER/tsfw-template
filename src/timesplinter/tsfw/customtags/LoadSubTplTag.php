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
 * @copyright Copyright (c) 2012, METANET AG, www.metanet.ch
 */
class LoadSubTplTag extends TemplateTag implements TagNode
{
	public function replaceNode(TemplateEngine $tplEngine, ElementNode $node)
	{
		$dataKey = $node->getAttribute('tplfile')->value;

		$tplFile = null;

		$tplFile = (preg_match('/^\{(.+)\}$/', $dataKey, $res) === 1)?'$this->getData(\'' . $res[1] . '\')':'\'' . $dataKey . '\'';

		/** @var TextNode */
		$newNode = new TextNode($tplEngine->getDomReader());
		$newNode->content = '<?php ' . __NAMESPACE__ . '\\LoadSubTplTag::requireFile(' . $tplFile . ', $this); ?>'; //$newTpl->getResultAsHtml();

		$node->parentNode->replaceNode($node, $newNode);
	}

	public function replaceInline(TemplateEngine $tplEngine, $nodeStr)
	{
		throw new TemplateEngineException('Don\'t use this tag (LoadSubTpl) inline!');
	}

	/**
	 * A special method that belongs to the LoadSubTplTag class but needs none
	 * static properties from this class and is called from the cached template
	 * files.
	 * @param string $file The full filepath to include (OR magic {this})
	 * @param TemplateEngine $tplEngine
	 */
	public static function requireFile($file, TemplateEngine $tplEngine)
	{
		$tplPath = explode(DIRECTORY_SEPARATOR, $tplEngine->getCurrentTemplateFile());
		array_pop($tplPath);
		$tplPathStr = implode(DIRECTORY_SEPARATOR, $tplPath) . DIRECTORY_SEPARATOR;

		echo $tplEngine->getResultAsHtml($tplPathStr . $file, (array)$tplEngine->getAllData());
	}

	/**
	 * @return string
	 */
	public static function getName()
	{
		return 'loadSubTpl';
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