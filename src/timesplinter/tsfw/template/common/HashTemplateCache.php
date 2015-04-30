<?php

namespace timesplinter\tsfw\template\common;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER
 */
class HashTemplateCache implements TemplateCacheStrategy
{
	const CACHE_SUFFIX = '.cache';
	
	protected $cachePath;
	protected $hashFilePath;
	protected $registry;
	protected $cacheChanged;

	public function __construct($cachePath, $hashFilePath)
	{
		$this->registry = array();
		$this->hashFilePath = $hashFilePath;
		
		$this->registry = $this->loadCacheFile();

		$this->cacheChanged = false;
	}

	private function loadCacheFile()
	{
		$cache = array();
		$cacheFilePath = $this->cachePath . $this->hashFilePath;

		if(file_exists($cacheFilePath) === false) {
			return $cache;
		}

		$content = json_decode(file_get_contents($cacheFilePath));
		
		$entries = array();
		
		foreach($content as $key => $entry) {
			$tplCacheEntry = new TemplateCacheEntry();
			
			$tplCacheEntry->changeTime = $entry->changeTime;
			$tplCacheEntry->templatePath = $entry->path;
			$tplCacheEntry->size = $entry->size;
			
			$entries[$key] = $tplCacheEntry;
		}
		
		return $entries;
	}

	protected function saveCacheFile()
	{
		if($this->cacheChanged === false)
			return;

		$cacheFilePath = $this->cachePath . $this->hashFilePath;

		if(file_put_contents($cacheFilePath, json_encode($this->registry)) === false)
			throw new TemplateEngineException('Could not write template cache-file: ' . $cacheFilePath);
	}

	/**
	 * @param string $tplFile
	 * 
	 * @return TemplateCacheEntry
	 */
	public function getCachedTplFile($tplFile)
	{
		if($this->registry === null || array_key_exists($tplFile, $this->registry) === false)
			return null;

		return $this->registry[$tplFile];
	}

	/**
	 * @param string $tplFile
	 * @param string $compiledTemplateContent
	 *
	 * @return TemplateCacheEntry
	 * @throws TemplateEngineException
	 */
	public function addCachedTplFile($tplFile, $compiledTemplateContent)
	{
		// NEW HERE
		$cacheFileName = uniqid() . self::CACHE_SUFFIX;
		$cacheFilePath = $this->cachePath . $cacheFileName;
		
		if(stream_resolve_include_path($cacheFilePath) === true && is_writable($cacheFilePath) === false)
			throw new TemplateEngineException('Cache file is not writable: ' . $cacheFilePath);

		if(file_put_contents($cacheFilePath, $compiledTemplateContent) === false)
			throw new TemplateEngineException('Could not cache template-file: ' . $cacheFilePath);

		$errorReportingLevel = error_reporting(0);
		$fileSize = filesize($tplFile);

		if(($changeTime = filemtime($tplFile)) === false)
			$changeTime = filectime($tplFile);
		
		error_reporting($errorReportingLevel);
		
		$tplCacheEntry = new TemplateCacheEntry();
		
		$tplCacheEntry->templatePath = $tplFile;
		$tplCacheEntry->cachePath = $cacheFileName;
		$tplCacheEntry->size = $fileSize;
		$tplCacheEntry->changeTime = $changeTime;
				
		$this->registry[$tplFile] = $tplCacheEntry;
		$this->cacheChanged = true;

		return $tplCacheEntry;
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

		if($cacheEntry->size !== @filesize($cacheEntry->templatePath) || $cacheEntry->changeTime < $changeTime)
			return false;

		return true;
	}
	
	public function __destruct()
	{
		$this->saveCacheFile($this->registry);
	}

	/**
	 * @param TemplateCacheEntry $cacheEntry
	 * @param string $compiledTemplateContent
	 *
	 * @return TemplateCacheEntry
	 * @throws TemplateEngineException
	 */
	public function updateCachedTplFile(TemplateCacheEntry $cacheEntry, $compiledTemplateContent)
	{
		// NEW HERE
		if(stream_resolve_include_path($cacheEntry->cachePath) === true && is_writable($cacheEntry->cachePath) === false)
			throw new TemplateEngineException('Cache file is not writable: ' . $cacheEntry->cachePath);

		if(file_put_contents($cacheEntry->cachePath, $compiledTemplateContent) === false)
			throw new TemplateEngineException('Could not cache template-file: ' . $cacheEntry->cachePath);

		$errorReportingLevel = error_reporting(0);
		$fileSize = filesize($cacheEntry->templatePath);

		if(($changeTime = filemtime($cacheEntry->templatePath)) === false)
			$changeTime = filectime($cacheEntry->templatePath);

		error_reporting($errorReportingLevel);

		$cacheEntry->size = $fileSize;
		$cacheEntry->changeTime = $changeTime;

		$this->cacheChanged = true;

		return $cacheEntry;
	}
}

/* EOF */