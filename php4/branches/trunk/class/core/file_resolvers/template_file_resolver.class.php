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
require_once(LIMB_DIR . '/class/core/file_resolvers/package_file_resolver.class.php');
require_once(LIMB_DIR . '/class/lib/util/ini.class.php');

class template_file_resolver extends package_file_resolver
{
  function _do_resolve($file_path)
  {
    $tmpl_path = get_ini_option('common.ini', 'path', 'Templates');
    
    $locale = $this->_get_locale_prefix();
    
    if(file_exists($tmpl_path . $locale . $file_path))
      return $tmpl_path . $locale . $file_path;
      
    if(file_exists($tmpl_path . $file_path))
      return $tmpl_path . $file_path;      
      
    if($resolved_path = $this->_find_file_in_packages('design/' . $locale . $file_path))
    {
      return $resolved_path;
    }
    elseif($resolved_path = $this->_find_file_in_packages('design/'  . $file_path))
    {
      return $resolved_path;
    }
    else
    {
  	  debug :: write_error('template not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
  	    array('file_path' => $file_path));
  	    
  	  return false;
    
    }
  }
  
  function _get_locale_prefix()
  {
  	if (defined('CONTENT_LOCALE_ID'))
  		$locale = '_' . CONTENT_LOCALE_ID . '/';
  	elseif(defined('DEFAULT_CONTENT_LOCALE_ID'))
  		$locale = '_' . DEFAULT_CONTENT_LOCALE_ID . '/';
  	else
  	  $locale = '';
  	  
  	return $locale;  
  }
}

?>