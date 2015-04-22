<?php

namespace timesplinter\tsfw\htmlparser;

/**
 * @author Pascal Münst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
class HtmlDoc
{
	protected $htmlContent;
	protected $contentPos;
	protected $nodeTree;
	protected $pendingNode;
	protected $namespace;
	protected $selfClosingTags;
	protected $currentLine;

	protected $tagPattern;
	
	public function __construct($htmlContent = null, $namespace = null)
	{
		$this->currentLine = 1; // Start at line 1 not 0, we are not nerds ;-)
		
		$this->htmlContent = $htmlContent;

		$this->nodeTree = new DocumentNode($this);
		$this->pendingNode = $this->nodeTree;

		$this->selfClosingTags = array('br', 'hr', 'img', 'input', 'link', 'meta');

		$this->namespace = $namespace;

		if($namespace !== null) {
			$this->tagPattern = '/(?:<!--.+?-->|<!\[CDATA\[.+?\]\]>|<(\/)?(' . $this->namespace . '\:\w+?)((?:\s+[^=]+="[^"]*")*?)?(\s*\/)?\s*>)/ims';
		} else {
			$this->tagPattern = '/(?:<!--.+?-->|<!\[CDATA\[.+?\]\]>|<(\/)?(\w+?)((?:\s+[^=]+="[^"]*")*?)?(\s*\/)?\s*>)/ims';
		}
	}

	/**
	 *
	 */
	public function parse()
	{
		if($this->htmlContent === null)
			return;

		$this->contentPos = 0;

		while($this->findNextNode() !== false);
		
		if($this->contentPos !== strlen($this->htmlContent)) {
			$restNode = new TextNode($this);
			$restNode->content = substr($this->htmlContent, $this->contentPos);
			$restNode->parentNode = $this->nodeTree;

			$this->nodeTree->addChildNode($restNode);
			
			$this->currentLine += substr_count($restNode->content, "\n");
		}
	}

	/**
	 * @return bool
	 */
	protected function findNextNode()
	{
		$oldPendingNode = $this->pendingNode;
		$oldContentPos = $this->contentPos;
		$res = null;
		
		if(preg_match($this->tagPattern, $this->htmlContent, $res, PREG_OFFSET_CAPTURE, $this->contentPos) === 0)
			return false; // If there is no tag left

		$this->currentLine += substr_count($res[0][0], "\n");
		$newPos = $res[0][1];

		if($oldContentPos !== $newPos) {
			// Control-Node
			$lostText = substr($this->htmlContent, $oldContentPos, ($newPos - $oldContentPos));
			$this->currentLine += substr_count($lostText, "\n");
			$lostTextNode = null;

			if(preg_match('/^\\s*$/', $lostText) === true) {
				$lostTextNode = new TextNode($this);
			} else {
				$lostTextNode = new TextNode($this);
			}

			$lostTextNode->content = $lostText;
			$lostTextNode->parentNode = $oldPendingNode;

			if($oldPendingNode === null) {
				$this->nodeTree->addChildNode($lostTextNode);
			} else {
				$oldPendingNode->addChildNode($lostTextNode);
			}
		}

		$this->contentPos = $newPos + strlen($res[0][0]);

		$newNode = null;
		
		if(strpos($res[0][0], '<!--') === 0) {
			// Comment-node
			$newNode = new CommentNode($this);
			$newNode->content = $res[0][0];
		} elseif(stripos($res[0][0], '<![CDATA[') === 0) {
			// CDATA-node
			$newNode = new CDataSectionNode($this);
			$newNode->content = $res[0][0];
		} elseif(stripos($res[0][0], '<!DOCTYPE') === 0) {
			$newNode = new DocumentTypeNode($this);
			$newNode->content = $res[0][0];
		} else {
			$newNode = new ElementNode($this);

			// </...> (close only)
			if(array_key_exists(1, $res) && $res[1][1] !== -1) {
				if($this->pendingNode instanceof ElementNode)
					$this->pendingNode->closed = true;
				
				$this->pendingNode = ($oldPendingNode !== null) ? $oldPendingNode->parentNode : null;
				
				/**
				 * @TODO That's dirty work here
				 */
				if($this->pendingNode === null) {
					$node = new TextNode($this);
					$node->content = '</' . $res[2][0] . '>';
					
					$this->nodeTree->addChildNode($node);
				}

				return true;
			}

			// Normal HTML-Tag-node
			$tagNParts = explode(':', $res[2][0]);

			if(count($tagNParts) > 1) {
				$newNode->namespace = $tagNParts[0];
				$newNode->tagName = $tagNParts[1];
			} else {
				$newNode->tagName = $tagNParts[0];
			}
			
			

			// <img ... /> (open and close)
			if((array_key_exists(4, $res) && $res[4][0] === '/') || (array_key_exists(3, $res) && $res[3][0] === '/') || in_array($res[2][0], $this->selfClosingTags)) {
				$newNode->tagType = ElementNode::TAG_SELF_CLOSING;
			} else {
				// (open only)
				$this->pendingNode = $newNode;
				$newNode->tagType = ElementNode::TAG_OPEN;
			}

			// Attributes
			if(array_key_exists(3, $res) && $res[3][0] !== '/') {
				preg_match_all('/(.+?)="(.*?)"/', $res[3][0], $resAttrs, PREG_SET_ORDER);

				foreach($resAttrs as $attr) {
					$newNode->addAttribute(new HtmlAttribute(trim($attr[1]), trim($attr[2])));
				}
			}
		}

		$newNode->line = $this->currentLine;
		$newNode->parentNode = $oldPendingNode;

		if($oldPendingNode === null) {
			$this->nodeTree->addChildNode($newNode);
		} else {
			$oldPendingNode->addChildNode($newNode);
		}

		return true;
	}

	/**
	 * @param string $namespace
	 * @param HtmlNode|null $entryNode
	 * @return array
	 */
	public function getNodesByNamespace($namespace, $entryNode = null)
	{
		$nodes = array();
		$nodeList = null;

		if($entryNode === null) {
			$nodeList = $this->nodeTree;
		} else {
			$nodeList = $entryNode->childNodes;
		}

		foreach($nodeList as $node) {
			if($node instanceof ElementNode === false)
				continue;

			/** @var ElementNode $node */

			if($node->namespace === $namespace)
				$nodes[] = $node;

			if(!$node->hasChildren())
				continue;

			$nodes = array_merge($nodes, $this->getNodesByNamespace($namespace, $node));
		}

		return $nodes;
	}

	/**
	 * @param string $tagName
	 * @param ElementNode|null $entryNode
	 * @return array
	 */
	public function getNodesByTagName($tagName, $entryNode = null)
	{
		$nodes = array();
		$nodeList = null;

		if($entryNode === null) {
			$nodeList = $this->nodeTree;
		} else {
			$nodeList = $entryNode->childNodes;
		}

		foreach($nodeList as $node) {
			if($node instanceof ElementNode === false)
				continue;

			/** @var ElementNode $node */

			if($node->tagName === $tagName)
				$nodes[] = $node;

			if(!$node->hasChildren())
				continue;

			$nodes = array_merge($nodes, $this->getNodesByTagName($tagName, $node));
		}

		return $nodes;
	}

	/**
	 * @param ElementNode|null $entryNode
	 * @return string
	 */
	public function getHtml($entryNode = null)
	{
		$html = '';
		$nodeList = null;

		if($entryNode === null) {
			$nodeList = $this->nodeTree->childNodes;
		} else {
			if($entryNode->hasChildren() === false)
				return $html;

			$nodeList = $entryNode->childNodes;
		}

		foreach($nodeList as $node) {
			if(($node instanceof ElementNode) === false) {
				$html .= $node->content;
				continue;
			}

			$tagStr = (($node->namespace !== null) ? $node->namespace . ':' : '') . $node->tagName;

			$attrs = array();
			foreach($node->attributesNamed as $key => $val) {
				$attrs[] = $key . '="' . $val->value . '"';
			}
			$attrStr = (count($attrs) > 0) ? ' ' . implode(' ', $attrs) : '';
			
			$html .= '<' . $tagStr . $attrStr . $node->tagExtension . (($node->tagType === ElementNode::TAG_SELF_CLOSING)?' /':'') . '>' . $node->content;


			if(($node->tagType === ElementNode::TAG_OPEN && $node->closed === true) || $node->tagType === ElementNode::TAG_CLOSE)
				$html .= $this->getHtml($node) . '</' . $tagStr . '>';
		}

		return $html;
	}

	public function replaceNode(HtmlNode $nodeSearch, HtmlNode $nodeReplace)
	{
		$parentSearchNode = $nodeSearch->getParentNode();
		$nodeList = null;

		if($parentSearchNode === null) {
			$nodeList = $this->nodeTree;
		} else {
			$nodeList = $nodeSearch->getParentNode()->childNodes;
		}

		$countChildren = count($nodeList);

		for($i = 0; $i < $countChildren; $i++) {
			if($nodeList[$i] !== $nodeSearch)
				continue;

			$nodeList[$i] = $nodeReplace;
			break;
		}

		if($parentSearchNode === null) {
			$this->nodeTree = $nodeList;
		} else {
			$parentSearchNode->setChildNodes($nodeList);
		}
	}

	/**
	 * @return DocumentNode
	 */
	public function getNodeTree()
	{
		return $this->nodeTree;
	}

	/**
	 * @param string $tagName
	 */
	public function addSelfClosingTag($tagName)
	{
		$this->selfClosingTags[] = $tagName;
	}

	/**
	 * @return int
	 */
	public function getCurrentLine()
	{
		return $this->currentLine;
	}
}

/* EOF */