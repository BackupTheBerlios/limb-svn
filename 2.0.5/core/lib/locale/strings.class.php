<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: strings.class.php 410 2004-02-06 10:46:51Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/debug/debug.class.php');
require_once(LIMB_DIR . 'core/lib/util/ini.class.php');

class strings
{
	var $_ini_objects = array();
	var $_path_cache = array();
	var $_cache = array();

	function strings()
	{		
	}
	
	function get($key, $filename='common', $language_id=null)
	{
		$strings =& strings :: instance();
			  
	  return $strings->_get_recursive($key, $filename, $language_id);
	}
	
	function _get_recursive($key, $filename, $language_id)
	{
		$path = $this->_get_path($filename, $language_id);
		
		if(isset($this->_cache[$path][$key]))
			return $this->_cache[$path][$key];
		
		if(isset($this->_ini_objects[$path]))
			$ini =& $this->_ini_objects[$path];
		else
		{	  	
			$ini =& ini :: instance($path);			
			$this->_ini_objects[$path] =& $ini;					
		}
		
		if(!($value = $ini->variable('constants', $key)))
	  {
		  if($ini->has_variable('extends', 'filename'))
		  {
		  	$extend_filename = $ini->variable('extends', 'filename');
		  	$value = $this->_get_recursive($key, $extend_filename, $language_id);
		  }
		}
		
		if($value)
			$this->_cache[$path][$key] = $value;
		
		return $value;
	}
		
	function _get_path($filename='common', $language_id=null)
	{	
		if(isset($this->_path_cache[$filename][$language_id]))
			return $this->_path_cache[$filename][$language_id];
			
  	if(!$language_id)
  	{
	  	if(defined('MANAGEMENT_LOCALE_ID'))
	  		$language_id = MANAGEMENT_LOCALE_ID;
	  	else
	  		$language_id = DEFAULT_MANAGEMENT_LOCALE_ID;
	  }

		if(file_exists(PROJECT_DIR . '/core/strings/' . $filename . '_' . $language_id . '.ini'))
  		$dir = PROJECT_DIR . '/core/strings/';
  	elseif(file_exists(LIMB_DIR . '/core/strings/' . $filename . '_' . $language_id . '.ini'))
  		$dir = LIMB_DIR . '/core/strings/';
  	else
  		error('strings file not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
  			array(
  				'filename' => $filename,
  				'language_id' => $language_id
  			));
  		
	  $path = $dir . $filename . '_' . $language_id . '.ini';
	  
	  $this->_path_cache[$filename][$language_id] = $path;
	  
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