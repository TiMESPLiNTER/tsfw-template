<?php

namespace timesplinter\tsfw\template\common;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
class DirectoryTemplateCache implements TemplateCacheStrategy
{
	const CACHE_SUFFIX = '.php';

	protected $baseDir;
	protected $baseDirLength;
	protected $cachePath;
	protected $registry;
	
	function __construct($cachePath, $baseDir = DIRECTORY_SEPARATOR)
	{
		$this->cachePath = $cachePath;
		$this->baseDir = $baseDir;
		$this->baseDirLength = strlen($baseDir);
		$this->registry = array();
	}
	
	/**
	 *
	 * @param string $tplFile
	 *
	 * @return TemplateCacheEntry|null
	 */
	public function getCachedTplFile($tplFile)
	{
		$cacheFilePath = $this->cachePath . $this->getCacheFileName($tplFile);
		
		if(file_exists($cacheFilePath) === false)
			return null;
		
		return $this->createTemplateCacheEntry($tplFile, $cacheFilePath);
	}

	/**
	 * @param string $tplFile
	 * @param string $compiledTemplateContent
	 *
	 * @return TemplateCacheEntry Path to the cached template
	 */
	public function addCachedTplFile($tplFile, $compiledTemplateContent)
	{
		$cacheFilePath = $this->cachePath . $this->getCacheFileName($tplFile);

		if(file_exists($cacheFilePath) === true) {
			file_put_contents($cacheFilePath, $compiledTemplateContent);
			
			return $this->createTemplateCacheEntry($tplFile, $cacheFilePath);
		}
		
		$fileLocation = pathinfo($cacheFilePath, PATHINFO_DIRNAME);

		if(is_dir($fileLocation) === false)
			mkdir($fileLocation, 0777, true);

		file_put_contents($cacheFilePath, $compiledTemplateContent);
		
		return $this->createTemplateCacheEntry($tplFile, $cacheFilePath);
	}

	/**
	 * @param string $tplFile
	 *
	 * @return string
	 */
	protected function getCacheFileName($tplFile)
	{
		$offset = (strpos($tplFile, $this->baseDir) !== false) ? $this->baseDirLength : 0;

		return preg_replace('/\.\w+$/', self::CACHE_SUFFIX, substr($tplFile, $offset));
	}
	
	protected function createTemplateCacheEntry($tplFile, $cacheFilePath)
	{
		if(isset($this->registry[$tplFile]) === true)
			return $this->registry[$tplFile];
		
		$templateCacheEntry = new TemplateCacheEntry();

		if(($changeTime = filemtime($cacheFilePath)) === false)
			$changeTime = filectime($cacheFilePath);
		
		$templateCacheEntry->templatePath = $tplFile;
		$templateCacheEntry->cachePath = $cacheFilePath;
		$templateCacheEntry->changeTime = $changeTime;

		return ($this->registry[$tplFile] = $templateCacheEntry);
	}

	/**
	 * @param TemplateCacheEntry $cacheEntry
	 * @param string $compiledTemplateContent
	 *
	 * @return TemplateCacheEntry
	 */
	public function updateCachedTplFile(TemplateCacheEntry $cacheEntry, $compiledTemplateContent)
	{
		// We don't need to preserve the cache entry because it's not loaded from a cache index
		return $this->addCachedTplFile($cacheEntry->templatePath, $compiledTemplateContent);
	}

	/**
	 * Returns a cache entry for the given template file if there is a valid one
	 *
	 * @param TemplateCacheEntry $cacheEntry Path to the template file that should be checked
	 *
	 * @return bool Is file cached or not
	 */
	public function isCacheEntryValid(TemplateCacheEntry $cacheEntry)
	{
		if(($changeTime = @filemtime($cacheEntry->templatePath)) === false)
			$changeTime = @filectime($cacheEntry->templatePath);

		if($cacheEntry->changeTime < $changeTime)
			return false;

		return true;
	}
}

/* EOF */