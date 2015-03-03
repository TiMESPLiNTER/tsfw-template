<?php

namespace timesplinter\tsfw\template;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2015, TiMESPLiNTER Webdevelopment
 */
class NullTemplateCache extends TemplateCacheStrategy
{

	protected $nullCacheFile;
	protected $nullCacheEntry;
	
	public function __construct()
	{
		$this->nullCacheFile = tempnam(sys_get_temp_dir(), 'tpl');
		
		$this->nullCacheEntry = new TemplateCacheEntry();
		$this->nullCacheEntry->path = $this->nullCacheFile;
		$this->nullCacheEntry->size = -1;
		$this->nullCacheEntry->changeTime = PHP_INT_MAX;
	}
	
	/**
	 *
	 * @param string $tplFile
	 *
	 * @return TemplateCacheEntry|null
	 */
	public function getCachedTplFile($tplFile)
	{
		return null;
	}

	/**
	 * @param string $tplFile
	 * @param TemplateCacheEntry|null $currentCacheEntry
	 * @param string $compiledTemplateContent
	 *
	 * @return TemplateCacheEntry Path to the cached template
	 */
	public function addCachedTplFile($tplFile, $currentCacheEntry, $compiledTemplateContent)
	{
		file_put_contents($this->nullCacheFile, $compiledTemplateContent);
		
		return $this->nullCacheEntry;
	}
}

/* EOF */