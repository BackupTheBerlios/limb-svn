<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
//inspired by EZpublish(http://ez.no) ini class

require_once(LIMB_DIR . '/class/lib/system/fs.class.php');
require_once(LIMB_DIR . '/class/lib/error/debug.class.php');

class ini
{ 
	// Variable to store the ini file values.
	protected $group_values; 
	// Stores the file path
	protected $file_path; 
	// Stores the path and file_path of the cache file
	protected $cache_file;

	protected $cache_dir = '';

	protected $charset = 'utf8';

	function __construct($file_path, $use_cache = null)
	{
		if ($use_cache === null)
			$use_cache = $this->is_cache_enabled();

		$this->file_path = $file_path;
		$this->use_cache = $use_cache;
		$this->cache_dir = VAR_DIR . '/ini/';

		$this->load();
	} 
	
	public function get_override_file()
	{
	  if(file_exists($this->file_path . '.override'))
	    return $this->file_path . '.override';
	  else
	    return false;
	}
	
	public function get_cache_file()
	{
	  return $this->cache_file;
	}
	
	// Returns the current instance of the given .ini file
	static public function instance($file_path, $use_cache = null)
	{
		$obj = null;

		$instance_name = 'global_ini_instance_' . md5($file_path);

		if (isset($GLOBALS[$instance_name]))
			return $GLOBALS[$instance_name];

		$obj = new ini($file_path, $use_cache);
		$GLOBALS[$instance_name] = $obj;

		return $obj;
	} 
	
	public function get_charset()
	{
	  return $this->charset;
	}
	
	// returns the file_path
	public function get_original_file()
	{
		return $this->file_path;
	} 
		
	// returns true if INI cache is enabled globally, the default value is true.
	public function is_cache_enabled()
	{
		return (!defined('INI_CACHING_ENABLED') || (defined('INI_CACHING_ENABLED') && constant('INI_CACHING_ENABLED')));
	} 

	/*
   Tries to load the ini file specified in the constructor or instance() function.
   If cache files should be used and a cache file is found it loads that instead.
  */
	protected function load()
	{
	  if(!file_exists($this->file_path))
	    throw new FileNotFoundException('ini file not found', $this->file_path);
	  
	  
		if ($this->use_cache)
			$this->_load_cache();
		else
			$this->_parse($this->file_path);
	} 

	/*
    Will load a cached version of the ini file if it exists,
    if not it will _parse the original file and create the cache file.
  */
	protected function _load_cache()
	{
		$this->reset();

		$cache_dir = $this->cache_dir;
    
    try
    {    
      fs :: mkdir($cache_dir);
    }
    catch(IOException $e)
    {
    	debug :: write_warning('could not create cache directory for ini',
		  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
		  array('cache_dir' => $cache_dir));

      $this->_parse();
      return;
    }
    
    if($override_file = $this->get_override_file())
      $this->cache_file = $this->cache_dir . md5($override_file) . '.php';
    else
		  $this->cache_file = $this->cache_dir . md5($this->file_path) . '.php';

		if ($this->_is_cache_valid())
		{
			$charset = null;
			$group_values = array();
			
			include($this->cache_file);
			
			$this->charset = $charset;
			$this->group_values = $group_values;
		  unset($group_values);		
		} 
		else
		{
			$this->_parse();
			$this->_save_cache();
	  }
	} 
	
	protected function _is_cache_valid()
	{
	  if(!file_exists($this->cache_file))
	    return false;
	  
	  $override_file = $this->get_override_file();
	  	
		if (filemtime($this->cache_file) > filemtime($this->file_path))
		{
		  if($override_file && filemtime($this->cache_file) < filemtime($override_file))
		    return false;
		  else
		    return true;
		}
	  
	  return false;
	}

	/*
   Stores the content of the INI object to the cache file
  */
	protected function _save_cache()
	{
		if (is_array($this->group_values))
		{
			$fp = @fopen($this->cache_file, 'w+');
			if ($fp === false)
			{
				debug::write_error("Couldn't create cache file '{$this->cache_file}', perhaps wrong permissions", 
				__FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__);
				return;
			} 

			fwrite($fp, "<?php\n");
			fwrite($fp, '$charset = "' . $this->charset . '";' . "\n");

			fwrite($fp, '$group_values = ' . var_export($this->group_values, true) . ";\n");

			fwrite($fp, "\n?>");
			fclose($fp);
		} 
	} 

	/*
    Parses either the override ini file or the standard file and then the append
    override file if it exists.
   */
	protected function _parse()
	{
		$this->reset();
		
		$this->_parse_file_contents($this->file_path);
		
		if($override_file = $this->get_override_file())
		  $this->_parse_file_contents($override_file);
	} 
	
	protected function _parse_file_contents($file_path)
	{
		$fp = @fopen($file_path, 'r');
		if (!$fp)
			return false;

		$size = filesize($file_path);
		
		if($size == 0)
		    return;
		
		$contents = fread($fp, $size);
		fclose($fp);

		$this->_parse_string($contents);	
	}

	protected function _parse_string(&$contents)
	{
		$lines =& preg_split("#\r\n|\r|\n#", $contents);
	  unset($contents);

		if ($lines === false)
		{
			debug::write_error("'{$this->file_path}' is empty", __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__);
			return false;
		} 

		$current_group = 'default';

		if (count($lines) == 0)
		  return false;

		// check for charset
		if (preg_match("/#charset[^=]*=(.+)/", $lines[0], $match))
		{
  		$this->charset = trim($match[1]);
		} 

		foreach ($lines as $line)
		{
			if (($line = trim($line)) == '')
				continue; 
			// removing comments after #, not after # inside ""

			$line = preg_replace('/([^"#]+|"(.*?)")|(#[^#]*)/', "\\1", $line); 
			// check for new group
			if (preg_match("#^\[(.+)\]\s*$#", $line, $new_group_name_array))
			{
				$new_group_name = trim($new_group_name_array[1]);
				$current_group = $new_group_name;
				
				if(!isset($this->group_values[$current_group]))
				  $this->group_values[$current_group] = array();
				continue;
			} 
			// check for variable
			if (preg_match("#^([a-zA-Z0-9_-]+)(\[([a-zA-Z0-9_-]*)\]){0,1}(\s*)=(.*)$#", $line, $value_array))
			{
				$var_name = trim($value_array[1]);

				$var_value = trim($value_array[5]);

				if (preg_match('/^"(.*)"$/', $var_value, $m))
					$var_value = $m[1];

				if ($value_array[2])
				{
					if ($value_array[3])
					{
						$key_name = $value_array[3];

						if (isset($this->group_values[$current_group][$var_name]) &&
								is_array($this->group_values[$current_group][$var_name]))
							$this->group_values[$current_group][$var_name][$key_name] = $var_value;
						else
							$this->group_values[$current_group][$var_name] = array($key_name => $var_value);
					} 
					else
					{
						if (isset($this->group_values[$current_group][$var_name]) &&
								is_array($this->group_values[$current_group][$var_name]))
							$this->group_values[$current_group][$var_name][] = $var_value;
						else
							$this->group_values[$current_group][$var_name] = array($var_value);
					} 
				} 
				else
				{
					$this->group_values[$current_group][$var_name] = $var_value;
				} 
			} 
		} 
	} 
	
	// removes the cache file if it exists.
	public function reset_cache()
	{
		if (file_exists($this->cache_file))
			unlink($this->cache_file);
	} 

	/*
   Removes all read data from .ini files.
  */
	public function reset()
	{
		$this->group_values = array();
	} 

	/*
    Reads a variable from the ini file.
    false is returned if the variable was not found.
  */
	public function get_option($var_name, $group_name = 'default')
	{
		if (!isset($this->group_values[$group_name]))
		{
			debug::write_notice('undefined group',
				__FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__,
				array('ini' => $this->file_path,
					'group' => $group_name,
					'option' => $var_name)
				);
		} 
		elseif (isset($this->group_values[$group_name][$var_name]))
		{
			return $this->group_values[$group_name][$var_name];
		} 
		else
		{
			debug::write_notice('undefined option',
				__FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__,
				array('ini' => $this->file_path,
					'group' => $group_name,
					'option' => $var_name)
				);
		} 

		return '';
	} 
	
	/*
    Reads a variable from the ini file and puts it in the parameter $variable.
    $variable is not modified if the variable does not exist
  */
	public function assign_option(&$variable, $var_name, $group_name = 'default')
	{
		if (!$this->has_option($var_name, $group_name))
		  return false;
		  
		$variable = $this->get_option($var_name, $group_name);			
		return true;
	} 	

	/*
    Checks if a variable is set. Returns true if the variable exists, false if not.
  */
	public function has_option($var_name, $group_name = 'default')
	{
		return isset($this->group_values[$group_name][$var_name]);
	} 
	// Checks if group $group_name is set. Returns true if the group exists, false if not.
	public function has_group($group_name)
	{
		return isset($this->group_values[$group_name]);
	} 
	// Fetches a variable group and returns it as an associative array.
	public function get_group($group_name)
	{
		if (isset($this->group_values[$group_name]))
			return $this->group_values[$group_name];

		debug::write_notice('undefined group',
			__FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__,
			array('ini' => $this->file_path,
				'group' => $group_name
				)
			);
		return null;
	} 

	// Returns group_values, which is a nicely named Array
	public function get_all()
	{
		return $this->group_values;
	} 
} 

?>