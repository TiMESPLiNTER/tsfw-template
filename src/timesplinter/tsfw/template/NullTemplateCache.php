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

	/**
	 * @param string|null $tmpDir The temp directory where the compiled files should be written to
	 *
	 * @throws TemplateEngineException If the temp directory is not writable
	 */
	public function __construct($tmpDir = null)
	{
		if($tmpDir === null)
			$tmpDir = sys_get_temp_dir();
		
		if(is_writable($tmpDir) === false)
			throw new TemplateEngineException('The temp directory ' . $tmpDir . ' is not writable');
		
		$this->nullCacheFile = tempnam($tmpDir, 'tpl');
		
		$this->nullCacheEntry = new TemplateCacheEntry();
		$this->nullCacheEntry->cachePath = $this->nullCacheFile;
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