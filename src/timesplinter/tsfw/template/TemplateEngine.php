<?php

namespace timesplinter\tsfw\template;

use timesplinter\tsfw\common\StringUtils;
use timesplinter\tsfw\htmlparser\CDataSectionNode;
use timesplinter\tsfw\htmlparser\ElementNode;
use timesplinter\tsfw\htmlparser\HtmlDoc;
use timesplinter\tsfw\htmlparser\TextNode;

/**
 * TemplateEngine
 *
 * @author Pascal Münst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
class TemplateEngine
{
	/** @var HtmlDoc */
	protected $htmlDoc;
	protected $tplNsPrefix;
	protected $dataPool;
	protected $dataTable;
	protected $customTags;

	protected $cached;

	/** @var TemplateCacheStrategy */
	protected $templateCacheInterface;
	protected $currentTemplateFile;

	/** @var TemplateTag */
	protected $lastTplTag;
	protected $logger;

	protected $getterMethodPrefixes;

	/**
	 * 
	 * @param TemplateCacheStrategy $tplCacheInterface The template cache object
	 * @param string $tplNsPrefix The prefix for custom tags in the template file
	 * @param array $customTags Additional custom tags to be loaded
	 * 
	 * @return TemplateEngine A template engine instance to render files
	 */
	public function __construct(TemplateCacheStrategy $tplCacheInterface, $tplNsPrefix, array $customTags = array())
	{
		$this->templateCacheInterface = $tplCacheInterface;
		$this->tplNsPrefix = $tplNsPrefix;
		$this->customTags = self::getDefaultCustomTags() + $customTags;

		$this->dataPool = new \ArrayObject();
		$this->dataTable = new \ArrayObject();

		$this->getterMethodPrefixes = array('get', 'is', 'has');
	}

	protected static function getDefaultCustomTags()
	{
		return array(
			'text' => 'timesplinter\tsfw\customtags\TextTag',
			'print' => 'timesplinter\tsfw\customtagsPrintTag',
			'checkboxOptions' => 'timesplinter\tsfw\customtags\CheckboxOptionsTag',
			'checkbox' => 'timesplinter\tsfw\customtags\CheckboxTag',
			'date' => 'timesplinter\tsfw\customtags\DateTag',
			'else' => 'timesplinter\tsfw\customtags\ElseTag',
			'for' => 'timesplinter\tsfw\customtags\ForTag',
			'if' => 'timesplinter\tsfw\customtags\IfTag',
			'elseif' => 'timesplinter\tsfw\customtags\ElseifTag',
			'loadSubTpl' => 'timesplinter\tsfw\customtags\LoadSubTplTag',
			'options' => 'timesplinter\tsfw\customtags\OptionsTag',
			'option' => 'timesplinter\tsfw\customtags\OptionTag',
			'radioOptions' => 'timesplinter\tsfw\customtags\RadioOptionsTag',
			'radio' => 'timesplinter\tsfw\customtags\RadioTag'
		);
	}

	protected function load()
	{
		$this->lastTplTag = null;
		$this->htmlDoc->parse();
		
		$nodeList = $this->htmlDoc->getNodeTree()->childNodes;

		if(count($nodeList) === 0)
			throw new TemplateEngineException('Invalid template-file: ' . $this->currentTemplateFile);

		try {
			$this->copyNodes($nodeList);
		} catch(\DOMException $e) {
			throw new TemplateEngineException('Error while processing the template file ' . $this->currentTemplateFile . ': ' . $e->getMessage());
		}
	}

	/**
	 * @param array $nodeList
	 *
	 * @throws \Exception
	 * @throws TemplateEngineException
	 */
	protected function copyNodes(array $nodeList)
	{
		foreach($nodeList as $node) {
			// Parse inline tags if activated
			if($node instanceof ElementNode === true) {
				$attrs = $node->attributes;
				$countAttrs = count($attrs);

				if($countAttrs > 0) {
					for($i = 0; $i < $countAttrs; ++$i)
						$attrs[$i]->value = $this->replaceInlineTag($attrs[$i]->value);
				}
			} else {
				if($node instanceof TextNode || /*$node instanceof CommentNode ||*/ $node instanceof CDataSectionNode)
					$node->content = $this->replaceInlineTag($node->content);
				
				continue;
			}
			
			if(count($node->childNodes) > 0)
				$this->copyNodes($node->childNodes);

			if($node->namespace !== $this->tplNsPrefix)
				continue;

			if(isset($this->customTags[$node->tagName]) === false)
				throw new TemplateEngineException('The custom tag "' . $node->tagName . '" is not registered in this template engine instance');

			$tagClassName = $this->customTags[$node->tagName];

			if(class_exists($tagClassName) === false)
				throw new TemplateEngineException('The Tag "' . $tagClassName . '" does not exist');

			// Initiate Tag-Class and call replace()-Method
			$tagInstance = new $tagClassName;

			if($tagInstance instanceof TemplateTag === false) {
				$this->templateCacheInterface->setSaveOnDestruct(false);
				throw new TemplateEngineException('The class "' . $tagClassName . '" does not implement the abstract class "TemplateTag" and is so recognized as an illegal class for a custom tag."');
			}
			
			/** @var TagNode $tagInstance */
			
			try {
				$tagInstance->replaceNode($this, $node);
			} catch(TemplateEngineException $e) {
				$this->templateCacheInterface->setSaveOnDestruct(false);
				throw $e;
			}

			$this->lastTplTag = $tagInstance;
		}
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 *
	 * @throws \Exception
	 * @throws TemplateEngineException
	 */
	protected function replaceInlineTag($value)
	{
		$inlineTags = null;
		
		preg_match_all('@\{' . $this->tplNsPrefix . ':(.+?)(?:\\s+(\\w+=\'.+?\'))?\\s*\}@', $value, $inlineTags, PREG_SET_ORDER);
		
		if(count($inlineTags) <= 0)
			return $value;

		for($j = 0; $j < count($inlineTags); $j++) {
			$tagName = $inlineTags[$j][1];

			if(isset($this->customTags[$tagName]) === false)
				throw new TemplateEngineException('The custom tag "' . $tagName . '" is not registered in this template engine instance');

			$tagClassName = $this->customTags[$tagName];

			$tagInstance = new $tagClassName;

			if($tagInstance instanceof TemplateTag === false) {
				$this->templateCacheInterface->setSaveOnDestruct(false);
				throw new TemplateEngineException('The class "' . $tagClassName . '" does not implement the abstract class "TemplateTag" and is so not recognized as an illegal class for a custom tag."');
			}

			if($tagInstance instanceof TagInline === false)
				throw new TemplateEngineException('CustomTag "' . $tagClassName . '" is not allowed to use inline.');

			/** @var TagInline $tagInstance */
			
			// Params
			$params = $parsedParams = array();

			if(array_key_exists(2, $inlineTags[$j])) {
				preg_match_all('@(?:(\\w+)=\'(.+?)\')@', $inlineTags[$j][2], $parsedParams, PREG_SET_ORDER);

				$countParams = count($parsedParams);
				for($p = 0; $p < $countParams; $p++)
					$params[$parsedParams[$p][1]] = $parsedParams[$p][2];
			}

			try {
				$repl = $tagInstance->replaceInline($this, $params);
				$value = str_replace($inlineTags[$j][0], $repl, $value);
			} catch(TemplateEngineException $e) {
				$this->templateCacheInterface->setSaveOnDestruct(false);
				throw $e;
			}
		}

		return $value;
	}

	/**
	 * This method parses the given template file
	 *
	 * @param string $tplFile The path to the template file to parse
	 *
	 * @return string The parsed template
	 */
	public function parse($tplFile)
	{
		if(($this->cached = $this->isTplFileCached($tplFile)) !== null)
			return $this->cached;
		
		// PARSE IT NEW: No NodeList given? Okay! I'll load defaults for you
		return $this->cache($tplFile);
	}

	/**
	 * Returns if file is cached or not
	 *
	 * @param string $filePath Path to the templace file that should be checked
	 *
	 * @return boolean Is file cached or not
	 *
	 * @throws TemplateEngineException
	 */
	private function isTplFileCached($filePath)
	{
		if(stream_resolve_include_path($filePath) === false)
			throw new TemplateEngineException('Could not find template file: ' . $filePath);

		/** @var TemplateCacheEntry */
		$tplCacheEntry = $this->templateCacheInterface->getCachedTplFile($filePath);
		
		if($tplCacheEntry === null)
			return null;

		if(($changeTime = @filemtime($filePath)) === false)
			$changeTime = @filectime($filePath);
		
		if(($tplCacheEntry->size >= 0 && $tplCacheEntry->size !== @filesize($filePath)) || $tplCacheEntry->changeTime < $changeTime) {
			return null;
		}

		return $tplCacheEntry;
	}

	/**
	 * Returns the final HTML-code or includes the cached file (if caching is
	 * enabled)
	 * @param string $tplFile
	 * @param array $tplVars
	 * @throws \Exception
	 * @return string
	 */
	public function getResultAsHtml($tplFile, $tplVars = array())
	{
		$this->currentTemplateFile = $tplFile;
		$this->dataPool = new \ArrayObject($tplVars);
		
		$templateCacheEntry = $this->parse($tplFile);

		try {
			ob_start();
			
			require $this->templateCacheInterface->getCachePath() . $templateCacheEntry->path;
			
			return ob_get_clean();
		} catch(\Exception $e) {
			// Throw away the whole template code till now
			ob_clean();

			// Throw the exception again
			throw $e;
		}
	}

	protected function cache($tplFile)
	{
		$cacheFileName = null;
		
		if(stream_resolve_include_path($tplFile) === false)
			throw new TemplateEngineException('Template file \'' . $tplFile . '\' does not exists');
		
		/** @var TemplateCacheEntry */
		$currentCacheEntry = $this->templateCacheInterface->getCachedTplFile($tplFile);
		
		// Render tpl
		$content = file_get_contents($tplFile);
		$this->htmlDoc = new HtmlDoc($content, $this->tplNsPrefix);

		foreach($this->customTags as $customTag) {
			if(in_array('timesplinter\tsfw\template\TagNode', class_implements($customTag)) === false || $customTag::isSelfClosing() === false)
				continue;
			
			/** @var TagNode $customTag */
			$this->htmlDoc->addSelfClosingTag($this->tplNsPrefix . ':' . $customTag::getName());
		}
		
		$this->load();

		$compiledTemplateContent = $this->htmlDoc->getHtml();
		$this->templateCacheInterface->setSaveOnDestruct(false);

		return $this->templateCacheInterface->addCachedTplFile($tplFile, $currentCacheEntry, $compiledTemplateContent);
	}

	/**
	 * @return HtmlDoc
	 */
	public function getDomReader()
	{
		return $this->htmlDoc;
	}

	/**
	 * Checks if a template node is followed by another template tag with a
	 * specific tagname.
	 * 
	 * @param ElementNode $tagNode The template tag
	 * @param string|array $tagName The tagname(s) of the following template tag(s)
	 * 
	 * @return bool
	 */
	public function isFollowedBy($tagNode, $tagName)
	{
		$nextSibling = $tagNode->getNextSibling();

		$res = !($nextSibling === null || $nextSibling->namespace !== $this->getTplNsPrefix() || in_array($nextSibling->tagName, (array)$tagName) === false);
		//var_dump($tagName, $res);
		
		return $res;
	}

	/**
	 * Register a value to make it accessible for the engine
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @param boolean $overwrite
	 * 
	 * @throws TemplateEngineException
	 */
	public function addData($key, $value, $overwrite = false)
	{
		if($this->dataPool->offsetExists($key) === true && $overwrite === false) {
			throw new TemplateEngineException("Data with the key '" . $key . "' is already registered");
		}

		$this->dataPool->offsetSet($key, $value);
	}

	public function unsetData($key)
	{
		if($this->dataPool->offsetExists($key) === false)
			return;

		$this->dataPool->offsetUnset($key);
	}

	/**
	 * Returns a registered data entry with the given key
	 * 
	 * @param string $key The key of the data element
	 * 
	 * @return mixed The value for that key or the key itselfs
	 */
	public function getData($key)
	{
		if($this->dataPool->offsetExists($key) === false)
			return null;

		return $this->dataPool->offsetGet($key);
	}

	public function getDataFromSelector($selector)
	{
		return $this->getSelectorValue($selector);
	}

	public function setAllData($dataPool)
	{
		foreach($dataPool as $key => $val)
			$this->dataPool->offsetSet($key, $val);
	}

	public function getAllData()
	{
		return $this->dataPool;
	}

	public function getTplNsPrefix()
	{
		return $this->tplNsPrefix;
	}

	public function getTemplateCacheInterface()
	{
		return $this->templateCacheInterface;
	}

	/**
	 * Returns the latest template tag found by the engine
	 * 
	 * @return TemplateTag
	 */
	public function getLastTplTag()
	{
		return $this->lastTplTag;
	}

	/**
	 * @return string The template file path which gets parsed at the moment
	 */
	public function getCurrentTemplateFile()
	{
		return $this->currentTemplateFile;
	}

	/**
	 * @param ElementNode $contextTag
	 * @param string|array $attrs
	 *
	 * @return bool
	 * 
	 * @throws TemplateEngineException
	 */
	public function checkRequiredAttrs($contextTag, $attrs)
	{
		foreach((array)$attrs as $a) {
			$val = $contextTag->getAttribute($a)->value;
			
			if($val !== null)
				continue;
			
			throw new TemplateParseException('Could not parse the template: Missing attribute \'' . $a .'\' for custom tag \'' . $contextTag->tagName . '\' in ' .  $this->currentTemplateFile . ' on line ' . $contextTag->line, $this->currentTemplateFile, $contextTag->line);
		}

		return true;
	}

	/**
	 * Register a new tag for the this TemplateEngine instance
	 *
	 * @param string $tagName The name of the tag
	 * @param string $tagClass The class name of the tag
	 */
	public function registerTag($tagName, $tagClass)
	{
		$this->customTags[$tagName] = $tagClass;
	}

	/**
	 * @param string $selectorStr
	 * @param bool $returnNull
	 *
	 * @return mixed
	 *
	 * @throws TemplateEngineException
	 */
	protected function getSelectorValue($selectorStr, $returnNull = false)
	{
		$selParts = explode('.', $selectorStr);
		$firstPart = array_shift($selParts);
		$currentSel = $firstPart;

		if($this->dataPool->offsetExists($firstPart) === false) {
			if($returnNull === false)
				throw new TemplateEngineException('The data with offset "' . $currentSel . '" does not exist for template file ' . $this->currentTemplateFile);

			return null;
		}

		$varData = $this->dataPool->offsetGet($firstPart);

		foreach($selParts as $part) {
			$nextSel = $currentSel . '.' . $part;

			// Try to find value in hashmap, thats faster then parse again
			/*if($this->dataTable->offsetExists($nextSel)) {
				$varData = $this->dataTable->offsetGet($nextSel);

				continue;
			}*/

			if($varData instanceof \ArrayObject === true) {
				/** @var \ArrayObject $varData */
				if($varData->offsetExists($part) === false)
					throw new TemplateEngineException('Array key "' . $part . '" does not exist in ArrayObject "' . $currentSel . '"');

				$varData = $varData->offsetGet($part);
			} elseif(is_object($varData) === true) {
				$args = array();

				if(($argPos = strpos($part, '(')) !== false) {
					$argStr = substr($part, $argPos + 1, -1);
					$part = substr($part, 0, $argPos);

					foreach(preg_split('/,/x', $argStr) as $no => $arg) {

						if(StringUtils::startsWith($argStr, '\'') === false || StringUtils::endsWith($argStr, '\'') === false)
							$args[$no] = $this->getSelectorValue($argStr, $returnNull);
						else
							$args[$no] = substr($arg, 1, -1);
					}
				}

				if(property_exists($varData, $part) === true) {
					$getProperty = new \ReflectionProperty($varData, $part);

					if($getProperty->isPublic() === true) {
						$varData = $varData->$part;
					} else {
						$getterMethodName = null;

						foreach($this->getterMethodPrefixes as $mp) {
							$getterMethodName = $mp . ucfirst($part);

							if(method_exists($varData, $getterMethodName) === true)
								break;

							$getterMethodName = null;
						}

						if($getterMethodName === null)
							throw new TemplateEngineException('Could not access protected/private property "' . $part . '". Please provide a getter method');

						$varData = call_user_func(array($varData, $getterMethodName));
					}
				} elseif(method_exists($varData, $part) === true) {
					$varData = call_user_func_array(array($varData, $part), $args);
				} else {
					throw new TemplateEngineException('Don\'t know how to handle selector part "' . $part . '"');
				}
			} elseif(is_array($varData) === true) {
				if(array_key_exists($part, $varData) === false)
					throw new TemplateEngineException('Array key "' . $part . '" does not exist in array "' . $currentSel . '"');

				$varData = $varData[$part];
			} else {
				throw new TemplateEngineException('The data with offset "' . $currentSel . '" is not an object nor an array.');
			}

			$currentSel = $nextSel;
			$this->dataTable->offsetSet($currentSel, $varData);
		}

		return $varData;
	}
}

/* EOF */