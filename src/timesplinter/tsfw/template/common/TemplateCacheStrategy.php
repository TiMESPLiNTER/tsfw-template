<?php

namespace timesplinter\tsfw\template\common;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
interface TemplateCacheStrategy 
{
	/**
	 * 
	 * @param string $tplFile
	 * @return TemplateCacheEntry|null
	 */
	public function getCachedTplFile($tplFile);

	/**
	 * @param string $tplFile
	 * @param string $compiledTemplateContent
	 *
	 * @return TemplateCacheEntry Path to the cached template
	 */
	public function addCachedTplFile($tplFile, $compiledTemplateContent);

	/**
	 * @param TemplateCacheEntry $cacheEntry
	 * @param string $compiledTemplateContent
	 *
	 * @return TemplateCacheEntry
	 */
	public function updateCachedTplFile(TemplateCacheEntry $cacheEntry, $compiledTemplateContent);

	/**
	 * Returns a cache entry for the given template file if there is a valid one
	 *
	 * @param TemplateCacheEntry $cacheEntry Path to the template file that should be checked
	 *
	 * @return bool Is file cached or not
	 */
	public function isCacheEntryValid(TemplateCacheEntry $cacheEntry);
}

/* EOF */