<?php

namespace timesplinter\tsfw\htmlparser;

/**
 * ElementNode
 *
 * @author Pascal Münst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
class ElementNode extends HtmlNode
{
	const TAG_OPEN = 1;
	const TAG_CLOSE = 2;
	const TAG_SELF_CLOSING = 3;

	public $childNodes;
	public $tagType;
	public $tagName;
	public $namespace;
	public $attributes;
	public $attributesNamed;
	public $tagExtension;
	public $closed;

	public function __construct(HtmlDoc $htmlDocument)
	{
		parent::__construct(HtmlNode::ELEMENT_NODE, $htmlDocument);
		
		$this->namespace = null;
		$this->tagName = null;
		$this->tagType = null;

		$this->attributes = array();
		$this->attributesNamed = array();
		$this->tagExtension = null;
		$this->closed = false;
	}

	/**
	 * 
	 * @param string $key
	 * @return HtmlAttribute
	 */
	public function getAttribute($key)
	{
		if(isset($this->attributesNamed[$key]) === false)
			return new HtmlAttribute($key, null);
		
		return $this->attributesNamed[$key];
	}

	public function addAttribute(HtmlAttribute $attr)
	{
		$this->attributes[] = $attr;
		$this->attributesNamed[$attr->key] = $attr;
	}

	public function doesAttributeExist($key)
	{
		return isset($this->attributesNamed[$key]);
	}
	
	public function removeAttribute($key)
	{
		if(isset($this->attributesNamed[$key]) === true)
			unset($this->attributesNamed[$key]);
	}

	/**
	 * @param ElementNode|null $entryNode
	 *
	 * @return string
	 */
	public function getInnerHtml($entryNode = null)
	{
		$html = '';
		$nodeList = null;

		if($entryNode === null) {
			$nodeList = $this->childNodes;
		} else {
			$nodeList = $entryNode->childNodes;
		}
		
		if($nodeList === null)
			return $html;
		
		foreach($nodeList as $node) {
			if($node instanceof ElementNode === false) {
				$html .= $node->content;
				continue;
			}

			$tagStr = (($node->namespace !== null) ? $node->namespace . ':' : '') . $node->tagName;

			$attrs = array();
			foreach($node->attributesNamed as $key => $val) {
				$attrs[] = $key . '="' . $val->value . '"';
			}
			$attrStr = (count($attrs) > 0) ? ' ' . implode(' ', $attrs) : '';

			if($node instanceof ElementNode === true) {
				$html .= '<' . $tagStr . $attrStr . $node->tagExtension . (($node->tagType === ElementNode::TAG_SELF_CLOSING) ? ' /' : '') . '>' . $node->content;
			} else {
				$html .= $node->content;
			}

			if($node->tagType === ElementNode::TAG_OPEN)
				$html .= $this->getInnerHtml($node) . '</' . $tagStr . '>';
		}

		return $html;
	}
}

/* EOF */