<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: cache_lite.class.php 441 2004-02-13 16:07:39Z server $
*
***********************************************************************************/ 

define('CACHE_LITE_ERROR_RETURN', 1);
define('CACHE_LITE_ERROR_DIE', 8);

require_once(LIMB_DIR . 'core/lib/system/dir.class.php');
require_once(LIMB_DIR . 'core/lib/debug/debug.class.php');

class cache_lite
{
	/**
	* Directory where to put the cache files
	* (make sure to add a trailing slash)
	* 
	* @var string $_cache_dir
	*/
	var $_cache_dir = '/tmp/';

	/**
	* Enable / disable caching
	* 
	* (can be very usefull for the debug of cached scripts)
	* 
	* @var boolean $_caching
	*/
	var $_caching = true;

	/**
	* Cache lifetime (in seconds)
	* 
	* @var int $_life_time
	*/
	var $_life_time = 3600;

	/**
	* File last modified time
	* 
	* @var int $_file_last_modified
	*/
	var $_file_last_modified = -1;

	/**
	* Enable / disable file_locking
	* 
	* (can avoid cache corruption under bad circumstances)
	* 
	* @var boolean $_file_locking
	*/
	var $_file_locking = true;

	/**
	* Timestamp of the last valid cache
	* 
	* @var int $_refresh_time
	*/
	var $_refresh_time;

	/**
	* File name (with path)
	* 
	* @var string $_file
	*/
	var $_file;

	/**
	* Enable / disable write control (the cache is read just after writing to detect corrupt entries)
	* 
	* Enable write control will lightly slow the cache writing but not the cache reading
	* Write control can detect some corrupt cache files but maybe it's not a perfect control
	* 
	* @var boolean $_write_control
	*/
	var $_write_control = true;

	/**
	* Enable / disable read control
	* 
	* If enabled, a control key is embeded in cache file and this key is compared with the one
	* calculated after the reading.
	* 
	* @var boolean $_read_control
	*/
	var $_read_control = true;

	/**
	* Type of read control (only if read control is enabled)
	* 
	* Available values are :
	* 'md5' for a md5 hash control (best but slowest)
	* 'crc32' for a crc32 hash control (lightly less safe but faster, better choice)
	* 'strlen' for a length only test (fastest)
	* 
	* @var boolean $_read_control_type
	*/
	var $_read_control_type = 'crc32';

	/**
	* Current cache id
	* 
	* @var string
	*/
	var $_id;

	/**
	* Current cache group
	* 
	* @var string
	*/
	var $_group;

	/**
	* Enable / Disable "Memory Caching"
	* 
	* NB : There is no lifetime for memory caching !
	* 
	* @var boolean
	*/
	var $_memory_caching = false;

	/**
	* Enable / Disable "Only Memory Caching"
	* (be carefull, memory caching is "beta quality")
	* 
	* @var boolean
	*/
	var $_only_memory_caching = false;

	/**
	* Memory caching array
	* 
	* @var array $_memory_caching_array
	*/
	var $_memory_caching_array = array();

	/**
	* Memory caching counter
	* 
	* @var int $memory_caching_counter
	*/
	var $_memory_caching_counter = 0;

	/**
	* Memory caching limit
	* 
	* @var int $memory_caching_limit
	*/
	var $_memory_caching_limit = 1000;

	/**
	* File Name protection
	* 
	* if set to true, you can use any cache id or group name
	* if set to false, it can be faster but cache ids and group names
	* will be used directly in cache file names so be carefull with
	* special characters...
	* 
	* @var boolean
	*/
	var $_file_name_protection = true; 
	
	/**
	* Constructor
	* 
	* $attributes is an assoc. Available attributes are :
	* 
	* @param array $attributes attributes
	* @access public 
	*/
	function cache_lite($attributes = array(null))
	{
		$this->import_attributes($attributes);

		if (!is_dir(CACHE_DIR))
			dir::mkdir(CACHE_DIR, 0777, true);
	} 

	/**
	* $attributes is an assoc. Available attributes are :
	* $attributes = array(
	*      'cache_dir' => directory where to put the cache files (string),
	*      'caching' => enable / disable caching (boolean),
	*      'life_time' => cache lifetime in seconds (int),
	* 		 'file_last_modified' => if set - cache file last modified date compared with this one
	*      'file_locking' => enable / disable file_locking (boolean),
	*      'write_control' => enable / disable write control (boolean),
	*      'read_control' => enable / disable read control (boolean),
	*      'read_control_type' => type of read control 'crc32', 'md5', 'strlen' (string),
	*      'memory_caching' => enable / disable memory caching (boolean),
	*      'only_memory_caching' => enable / disable only memory caching (boolean),
	*      'memory_caching_limit' => max nbr of records to store into memory caching (int)
	* );
	* 
	* @param array $attributes attributes
	* @access public 
	*/
	function import_attributes($attributes = array(null))
	{
		$available_attributes = '{file_name_protection}{memory_caching}{only_memory_caching}{memory_caching_limit}{cache_dir}{caching}{life_time}{file_last_modified}{file_locking}{write_control}{read_control}{read_control_type}';
		while (list($key, $value) = each($attributes))
		{
			if (strpos('>' . $available_attributes, '{' . $key . '}'))
			{
				$property = '_' . $key;
				$this->$property = $value;
			} 
		} 
	} 

	function is_cache_enabled()
	{
		return (!defined('CACHING_ENABLED') || (defined('CACHING_ENABLED') && constant('CACHING_ENABLED')));
	} 

	/**
	* Test if a cache is available and (if yes) return it
	* 
	* @param string $id cache id
	* @param string $group name of the cache group
	* @param boolean $do_not_test_cache_validity if set to true, the cache validity won't be tested
	* @return string data of the cache (or false if no cache available)
	* @access public 
	*/
	function &get($id, $group = 'default', $do_not_test_cache_validity = false)
	{
		$this->_caching = $this->is_cache_enabled();

		$this->_id = $id;
		$this->_group = $group;
		$data = false;

		if ($this->_caching)
		{
			$this->_set_file_name($id, $group);

			if ($this->_memory_caching)
			{
				if (isset($this->_memory_caching_array[$this->_file]))
					return $this->_memory_caching_array[$this->_file];
				elseif ($this->_only_memory_caching)
					return false;
			} 
			if ($do_not_test_cache_validity)
			{
				if (file_exists($this->_file))
					$data = $this->_read();
			} elseif ($this->_file_last_modified > -1)
			{
				if (file_exists($this->_file) && $this->_file_last_modified <= filemtime($this->_file))
					$data = $this->_read();
			} 
			else
			{
				if (file_exists($this->_file) && filemtime($this->_file) > time())
					$data = $this->_read();
			} 
			if (($data) && ($this->_memory_caching))
				$this->_memory_cache_add($this->_file, $data);

			return $data;
		} 
		return false;
	} 

	/**
	* Save some data in a cache file
	* 
	* @param string $data data to put in cache
	* @param string $id cache id
	* @param string $group name of the cache group
	* @return boolean true if no problem
	* @access public 
	*/
	function save($data, $id = null, $group = 'default')
	{
		if ($this->_caching)
		{
			if (isset($id))
				$this->_set_file_name($id, $group);

			if ($this->_memory_caching)
			{
				$this->_memory_cache_add($this->_file, $data);
				if ($this->_only_memory_caching)
					return true;
			} 
			if ($this->_write_control)
			{
				if (!$this->_write_and_control($data))
				{
					unlink($this->_file);
					return false;
				} 
				else
					return true;
			} 
			else
				return $this->_write($data);
		} 
		return false;
	} 

	/**
	* Remove a cache file
	* 
	* @param string $id cache id
	* @param string $group name of the cache group
	* @return boolean true if no problem
	* @access public 
	*/
	function remove($id, $group)
	{
		$this->_set_file_name($id, $group);
		if (!@unlink($this->_file))
		{
			debug::write_error('Unable to remove cache !', __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__);
			return false;
		} 
		return true;
	} 

	/**
	* Clean the cache
	* 
	* if no group is specified all cache files will be destroyed
	* else only cache files of the specified group will be destroyed
	* 
	* @param string $group name of the cache group
	* @return boolean true if no problem
	* @access public 
	*/
	function clean($group = false)
	{
		if ($this->_file_name_protection)
			$motif = ($group) ? 'cache_' . md5($group) . '_' : 'cache_';
		else
			$motif = ($group) ? 'cache_' . $group . '_' : 'cache_';

		if ($this->_memory_caching)
		{
			while (list($key, $value) = each($this->_memory_caching))
			{
				if (strpos($key, $motif, 0))
				{
					unset($this->_memory_caching[$key]);
					$this->_memory_caching_counter = $this->_memory_caching_counter - 1;
				} 
			} 
			if ($this->_only_memory_caching)
				return true;
		} 

		if (!($dh = opendir($this->_cache_dir)))
		{
			debug::write_error('Unable to open cache directory !', __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__);
			return false;
		} 

		while ($file = readdir($dh))
		{
			if (($file != '.') && ($file != '..'))
			{
				$file = $this->_cache_dir . $file;
				if (is_file($file))
				{
					if (strpos($file, $motif, 0))
					{
						if (!@unlink($file))
						{
							debug::write_error('Unable to remove cache !', __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__);
							return false;
						} 
					} 
				} 
			} 
		} 
		return true;
	} 

	/**
	* Set a new life time
	* 
	* @param int $new_life_time new life time (in seconds)
	* @access public 
	*/
	function set_life_time($new_life_time)
	{
		$this->_life_time = $new_life_time;
	} 

	/**
	* Set a cache dir
	* 
	* @param string $cache_dir 
	* @access public 
	*/
	function set_cache_dir($cache_dir)
	{
		$this->_cache_dir = $cache_dir;
	} 

	/**
	* 
	* @access public 
	*/
	function save_memory_caching_state($id, $group = 'default')
	{
		if ($this->_caching)
		{
			$array = array('counter' => $this->_memory_caching_counter,
				'array' => $this->_memory_caching_state
				);
			$data = serialize($array);
			$this->save($data, $id, $group);
		} 
	} 

	/**
	* 
	* @access public 
	*/
	function is_debug_enabled()
	{
		return (defined('DEBUG_CACHE_LITE_ENABLED') && constant('DEBUG_CACHE_LITE_ENABLED'));
	} 

	/**
	* 
	* @access public 
	*/
	function get_memory_caching_state($id, $group = 'default', $do_not_test_cache_validity = false)
	{
		if ($this->_caching)
		{
			if ($data = $this->get($id, $group, $do_not_test_cache_validity))
			{
				$array = unserialize($data);
				$this->_memory_caching_counter = $array['counter'];
				$this->_memory_caching_array = $array['array'];
			} 
		} 
	} 
	
	/**
	* 
	* @access private 
	*/
	function _memory_cache_add($id, $data)
	{
		$this->_memory_caching_array[$this->_file] = $data;
		if ($this->_memory_caching_counter >= $this->_memory_caching_limit)
		{
			list($key, $value) = each($this->_memory_caching_array);
			unset($this->_memory_caching_array[$key]);
		} 
		else
			$this->_memory_caching_counter = $this->_memory_caching_counter + 1;
	} 

	/**
	* Make a file name (with path)
	* 
	* @param string $id cache id
	* @param string $group name of the group
	* @access private 
	*/
	function _set_file_name($id, $group)
	{
		if ($this->_file_name_protection)
			$this->_file = ($this->_cache_dir . md5($group . $id));
		else
			$this->_file = $this->_cache_dir . 'cache_' . $group . '_' . $id;
	} 

	/**
	* Read the cache file and return the content
	* 
	* @return string content of the cache file
	* @access private 
	*/
	function _read()
	{
		$fp = @fopen($this->_file, 'r');
		if ($this->_file_locking) @flock($fp, LOCK_SH);

		if ($fp)
		{
			if ($this->is_debug_enabled())
				debug::write_notice("cache '{$this->_id}' group '{$this->_group}' file read", 'cache_lite::_read()');

			clearstatcache(); // because the filesize can be cached by PHP itself...
			$length = @filesize($this->_file);
			$mqr = get_magic_quotes_runtime();
			set_magic_quotes_runtime(0);

			if ($this->_read_control)
			{
				$hash_control = @fread($fp, 32);
				$length = $length - 32;
			} 

			$data = @fread($fp, $length);
			set_magic_quotes_runtime($mqr);

			if ($this->_file_locking)
				@flock($fp, LOCK_UN);

			@fclose($fp);
			if ($this->_read_control)
			{
				$hash_data = $this->_hash($data, $this->_read_control_type);
				if ($hash_data != $hash_control)
				{
					unlink($this->_file);

					if ($this->is_debug_enabled())
						debug::write_notice("cache '{$this->_id}' group '{$this->_group}' hash failed", 'cache_lite::_read()');

					return false;
				} 
			} 
			if ($this->is_debug_enabled())
				debug::write_notice("cache '{$this->_id}' group '{$this->_group}' retrieved", 'cache_lite::_read()');

			return $data;
		} 
		debug::write_error('Unable to read cache !', __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__);
		return false;
	} 

	/**
	* Write the given data in the cache file
	* 
	* @param string $data data to put in cache
	* @return boolean true if ok
	* @access private 
	*/
	function _write($data)
	{
		$fp = @fopen($this->_file, "w");
		if ($fp)
		{
			if ($this->_file_locking)
				@flock($fp, LOCK_EX);

			if ($this->_read_control)
				@fwrite($fp, $this->_hash($data, $this->_read_control_type), 32);

			$len = strlen($data);
			@fwrite($fp, $data, $len);
			if ($this->_file_locking)
				@flock($fp, LOCK_UN);
			@fclose($fp);

			if ($this->_file_last_modified > -1)
				@touch($this->_file, $this->_file_last_modified);
			else
				@touch($this->_file, time() + $this->_life_time);

			if ($this->is_debug_enabled())
				debug::write_notice("cache '{$this->_id}' group '{$this->_group}' written", 'cache_lite::_write()');

			return true;
		} 
		debug::write_error('Unable to write cache !', 
			__FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__,
			array(
				'file' => $this->_file
			)
		);
	} 

	/**
	* Write the given data in the cache file and control it just after to avoir corrupted cache entries
	* 
	* @param string $data data to put in cache
	* @return boolean true if the test is ok
	* @access private 
	*/
	function _write_and_control($data)
	{
		$this->_write($data);
		$data_read = $this->_read($data);
		return ($data_read == $data);
	} 

	/**
	* Make a control key with the string containing datas
	* 
	* @param string $data data
	* @param string $controlType type of control 'md5', 'crc32' or 'strlen'
	* @return string control key
	* @access private 
	*/
	function _hash($data, $controlType)
	{
		switch ($controlType)
		{
			case 'md5':
				return md5($data);
			case 'crc32':
				return sprintf('% 32d', crc32($data));
			case 'strlen':
				return sprintf('% 32d', strlen($data));
			default:
				debug::write_error('Unknown controlType ! (available values are only \'md5\', \'crc32\', \'strlen\')', __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__);
				return false;
		} 
	} 
} 

?>