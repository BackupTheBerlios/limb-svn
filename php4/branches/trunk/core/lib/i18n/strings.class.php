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
require_once(LIMB_DIR . '/core/lib/error/error.inc.php');
require_once(LIMB_DIR . '/core/lib/util/ini.class.php');

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
			  
	  return $strings->_get_recursive($key, $filename, $locale_id);
	}
	
	function _get_recursive($key, $filename, $locale_id)
	{
		$path = $this->_get_path($filename, $locale_id);
		$md5_path = md5($path);		
		if(isset($this->_cache[$md5_path][$key]))
			return $this->_cache[$md5_path][$key];
		
		if(isset($this->_ini_objects[$md5_path]))
			$ini =& $this->_ini_objects[$md5_path];
		else
		{	  	
			$ini =& ini :: instance($path);			
			$this->_ini_objects[$md5_path] =& $ini;					
		}
		
		if(!($value = $ini->get_option($key, 'constants')))
	  {
		  if($ini->has_option('filename', 'extends'))
		  {
		  	$extend_filename = $ini->get_option('filename', 'extends');
		  	$value = $this->_get_recursive($key, $extend_filename, $locale_id);
		  }
		}
		
		if($value)
			$this->_cache[$md5_path][$key] = $value;

		return $value;
	}
		
	function _get_path($filename='common', $locale_id)
	{					  

		if(isset($this->_path_cache[$filename][$locale_id]))
			return $this->_path_cache[$filename][$locale_id];	  
		
		if(file_exists(PROJECT_DIR . '/core/strings/' . $filename . '_' . $locale_id . '.ini'))
  		$dir = PROJECT_DIR . '/core/strings/';
  	elseif(file_exists(LIMB_DIR . '/core/strings/' . $filename . '_' . $locale_id . '.ini'))
  		$dir = LIMB_DIR . '/core/strings/';
  	else
  		error('strings file not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
  			array(
  				'filename' => $filename,
  				'locale_id' => $locale_id
  			));
  		
	  $path = $dir . $filename . '_' . $locale_id . '.ini';
	  
	  $this->_path_cache[$filename][$locale_id] = $path;
	  
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