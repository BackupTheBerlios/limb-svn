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
require_once(LIMB_DIR . '/class/core/packages_info.class.php');

class package_file_resolver
{
  protected $_packages_info = null;
  protected $_resolved_file_paths = array();
  
  function __construct()
  {
    $this->_packages_info = packages_info :: instance();
  }
    
  public function resolve($file_path)
  {
    if (isset($this->_resolved_file_paths[$file_path]))
      return $this->_resolved_file_paths[$file_path];
  
    $resolved_file_path = $this->_do_resolve($file_path);
    
    $this->_resolved_file_paths[$file_path] = $resolved_file_path;
    
    return $resolved_file_path;
  }
  
  protected function _do_resolve($file_path)
  {
    return $this->_find_file_in_packages($file_path);
  }
    
  protected function _find_file_in_packages($file_path)
  {
    $packages = $this->get_packages();
    
    foreach($packages as $package)
    {
      if (!isset($package['path']))
        continue;
              
      $resolved_file_path = $package['path'] . $file_path;
      if (file_exists($resolved_file_path))
        return $resolved_file_path;
    }
    
    return false;
  }
  
  public function get_packages()
  {
    return $this->_packages_info->get_packages();
  }

}

?>