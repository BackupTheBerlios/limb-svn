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
require_once(LIMB_DIR . 'class/lib/system/objects_support.inc.php');

class packages_info
{
  var $_packages = array();
  
  function & instance()
  {
    $obj =&	instantiate_object('packages_info');
    return $obj;
  }
  
  function get_packages()
  {
    if(!$this->_packages)
      $this->_load_packages();
    
    return $this->_packages;
  }  
  
  function _load_packages()
  {
    include_once(LIMB_DIR . '/class/lib/util/ini.class.php');
    
    $ini =& get_ini('packages.ini');
    $this->_packages = array();
    
    $groups = $ini->get_all();

    foreach($groups as $group => $data)
    {
      $data['path'] = $this->_parse_path($data['path']);
      
      $this->_define_package_constant($group, $data['path']);
            
      $this->_packages[] = $data;
    }
  }
  
  function _define_package_constant($package_name, $path)
  {
    if(!defined($package_name . '_DIR'))
      define($package_name . '_DIR', $path);  
  }
  
  function _parse_path($path)
  {
    return preg_replace('~\{([^\}]+)\}~e', "constant('\\1')", $path);
  }  
  
}

?>