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
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolver_decorator.class.php');
require_once(LIMB_DIR . '/class/lib/util/ini_support.inc.php');

class template_file_resolver extends file_resolver_decorator
{
  public function resolve($file_path, $params = array())
  {
    $tmpl_path = Limb :: toolkit()->getINI('common.ini')->get_option('path', 'Templates');
    
    $locale = $this->_get_locale_prefix();
    
    if(file_exists($tmpl_path . $locale . $file_path))
      return $tmpl_path . $locale . $file_path;
      
    if(file_exists($tmpl_path . $file_path))
      return $tmpl_path . $file_path;      
    
    try
    {  
      $resolved_path = $this->_resolver->resolve('design/' . $locale . $file_path, $params);
    }
    catch(FileNotFoundException $e)
    {
      $resolved_path = $this->_resolver->resolve('design/'  . $file_path, $params);
    }
    
    return $resolved_path;
  }
  
  protected function _get_locale_prefix()
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