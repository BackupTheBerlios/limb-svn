<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 

require_once(LIMB_DIR . '/core/lib/cache/cache_lite.class.php');

/**
* The block tag can be used to show or hide the contents of the block.
* The block_component provides an API which allows the block to be shown
* or hidden at runtime.
*/
class litecache_component extends component
{
	/**
	* Whether caching is on or off
	* 
	* @var int 0 or 1 for enabled / disabled caching
	* @access private 
	*/
	var $caching;
	/**
	* Instance of cache_lite
	* 
	* @var cache_lite
	* @access private 
	*/
	var $cache;
	/**
	* Name of compiled template file
	* 
	* @var string 
	* @access private 
	*/
	var $codefile;
	/**
	* Name of a dataspace variable which defines seperate cacheable content
	* such as the contents of $_GET['page']
	* 
	* @var string 
	* @access private 
	*/
	var $cacheby = '';
	/**
	* A group by which to identify to the file
	* 
	* @var string 
	* @access private 
	*/
	var $cachegroup = false;
	/**
	* Rendered HTML stored here
	* 
	* @var mixed 
	* @access private 
	*/
	var $output = '';
	/**
	* Constructs the litecache_component
	* 
	* @param int $ number of seconds after which cache file expires
	* @param string $ name of compiled template file
	* @param string $ dataspace variable name defining seperate cacheable content
	* @param string $ cache group - identifies a group of cache files
	* @access public 
	*/
	function litecache_component($codefile, $expires = '3600', $cacheby = '', $cachegroup = false)
	{
		$this->codefile = $codefile;

		$attributes = array();

		$attributes['caching'] = true;
		$this->caching = 1;

		$attributes['life_time'] = $expires;
		
		$attributes['cache_dir'] = CACHE_DIR;

		$this->cache =& new cache_lite($attributes);
		$this->cacheby = $cacheby;
		$this->cachegroup = $cachegroup;
	} 
	/**
	* Returns the ID used by Cache_Lite to identify the cache file
	* 
	* @return void 
	* @access public 
	*/
	function get_cache_id()
	{
		if ($this->get($this->cacheby))
			return $this->codefile . $this->get($this->cacheby);
		else
			return $this->codefile;
	} 
	/**
	* Returns the name of the cache group
	* 
	* @param string $ 
	* @return void 
	* @access public 
	*/
	function get_cache_group()
	{
		if ($this->get($this->cachegroup))
			return $this->get($this->cachegroup);
		else
			return 'default';
	} 
	/**
	* Returns the filename name of the cache file.
	* It's potentially "dangerous" as it has to access private parts of
	* 
	* @param string $ 
	* @return void 
	* @access private 
	*/
	function get_cache_file_name()
	{
		$this->cache->_set_file_name($this->get_cache_id(), $this->get_cache_group());
		return $this->cache->_file;
	} 
	/**
	* Determine whether template is cached
	* 
	* @return boolean true means template is cached
	* @access public 
	*/
	function is_cached()
	{
		if ($this->caching == 1)
		{
			if ($this->output = $this->cache->get($this->get_cache_id(), $this->get_cache_group()))
				return true;
		} 
		return false;
	} 
	/**
	* Cache output for this template
	* 
	* @param string $ parsed template output
	* @return void 
	* @access protected 
	*/
	function cache($output)
	{
		$this->output = $output;
		$this->cache->save($output, $this->get_cache_id(), $this->get_cache_group());
	} 
	/**
	* Delete this cache file
	* 
	* @return void 
	* @access public 
	*/
	function flush()
	{
		$this->cache->remove($this->get_cache_id(), $this->get_cache_group());
	} 
	/**
	* Flush all the cache files in this group.
	* 
	* @param mixed $ (optional) group name as string or nothing
	* @return void 
	* @access public 
	*/
	function flush_group()
	{
		$this->cache->clean($this->get_cache_group());
	} 
	/**
	* Returns the time the cache was last modified to help with
	* 
	* @return int 
	* @access public 
	*/
	function last_modified()
	{
		$file = $this->get_cache_file_name();
		if (file_exists($file))
			return filemtime($file);
		else
			return time();
	} 
	/**
	* Returns the output to be displayed
	* 
	* @return mixed either string or false is template not parsed or cached
	* @access public 
	*/
	function render()
	{
		echo $this->output;
	} 
} 

?>