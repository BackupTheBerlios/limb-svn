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

final class strings
{
  static protected $_instance = null;
  
	protected $_ini_objects = array();
	protected $_path_cache = array();
	protected $_cache = array();
	
	static function instance()
	{
    if (!self :: $_instance)
      self :: $_instance = new strings();

    return self :: $_instance;	
	}	
	
	static public function get($key, $filename='common', $locale_id=null)
	{
  	if(!$locale_id)
  	{
	  	if(defined('MANAGEMENT_LOCALE_ID'))
	  		$locale_id = MANAGEMENT_LOCALE_ID;
	  	else
	  		$locale_id = DEFAULT_MANAGEMENT_LOCALE_ID;
	  }		
			  
	  return self :: instance()->_do_get($key, $filename, $locale_id);
	}
	
	protected function _do_get($key, $filename, $locale_id)
	{
		$path = $this->_get_path($filename, $locale_id);
		
		if(isset($this->_cache[$path][$key]))
			return $this->_cache[$path][$key];
		
		if(isset($this->_ini_objects[$path]))
			$ini = $this->_ini_objects[$path];
		else
		{
		  $ini = ini :: instance($path);
			$this->_ini_objects[$path] = $ini;
		}
		
		if($value = $ini->get_option($key, 'constants'))
			$this->_cache[$path][$key] = $value;

		return $value;
	}
		
	protected function _get_path($file_name, $locale_id)
	{					  
		if(isset($this->_path_cache[$file_name][$locale_id]))
			return $this->_path_cache[$file_name][$locale_id];	  
			
    resolve_handle($resolver =& get_file_resolver('strings'));
    $path = $resolver->resolve($file_name, $locale_id);
			
	  $this->_path_cache[$file_name][$locale_id] = $path;
	  
	  return $path;
	}
}

?>