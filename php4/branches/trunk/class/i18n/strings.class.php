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
require_once(LIMB_DIR . 'class/lib/error/error.inc.php');
require_once(LIMB_DIR . 'class/lib/util/ini.class.php');

class strings
{
	var $_ini_objects = array();
	var $_path_cache = array();
	var $_cache = array();
	
	function get($key, $filename='common', $locale_id=null)
	{
		$strings =& strings :: instance();
		
  	if(!$locale_id)
  	{
	  	if(defined('MANAGEMENT_LOCALE_ID'))
	  		$locale_id = MANAGEMENT_LOCALE_ID;
	  	else
	  		$locale_id = DEFAULT_MANAGEMENT_LOCALE_ID;
	  }		
			  
	  return $strings->_do_get($key, $filename, $locale_id);
	}
	
	function _do_get($key, $filename, $locale_id)
	{
		$path = $this->_get_path($filename, $locale_id);
		
		if(isset($this->_cache[$path][$key]))
			return $this->_cache[$path][$key];
		
		if(isset($this->_ini_objects[$path]))
			$ini =& $this->_ini_objects[$path];
		else
		{	  	
			$ini =& ini :: instance($path);			
			$this->_ini_objects[$path] =& $ini;
		}
		
		if($value = $ini->get_option($key, 'constants'))
			$this->_cache[$path][$key] = $value;

		return $value;
	}
		
	function _get_path($file_name, $locale_id)
	{					  
		if(isset($this->_path_cache[$file_name][$locale_id]))
			return $this->_path_cache[$file_name][$locale_id];	  
			
    $resolver =& get_file_resolver('strings');
    resolve_handle($resolver);
    $path = $resolver->resolve($file_name, $locale_id);
			
	  $this->_path_cache[$file_name][$locale_id] = $path;
	  
	  return $path;
	}
	
 	function & instance()
  {   	
  	if(!isset($GLOBALS['global_strings_instance']))
			$GLOBALS['global_strings_instance'] =& new strings();
			  	
  	return $GLOBALS['global_strings_instance'];
  }	
}

?>