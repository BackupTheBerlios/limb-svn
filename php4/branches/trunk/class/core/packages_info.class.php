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
  
  function reset()
  {
    $this->_packages = array();
  }
  
  function get_packages()
  {
    if(!$this->_packages)
      $this->load_packages();
    
    return $this->_packages;
  }  
  
  function load_packages()
  {
    include_once(LIMB_DIR . '/class/lib/util/ini.class.php');
    
    $ini =& get_ini('packages.ini');
    $this->_packages = array();
    
    $groups = $ini->get_all();
    
    $packages = $ini->get_option('packages');
    
    if (!count($packages))
    {
   		debug :: write_error('no packages in package.ini!',
  		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);

      return false;
    }
    
    foreach($packages as $package_path)
    {
      $package_data = array();
      
      $package_path = $this->_parse_path($package_path);
      
      include($package_path . '/setup.php');
      
      $this->_define_package_constant($PACKAGE_NAME, $package_path);
      
      $package_data['path'] = $package_path;
      $package_data['name'] = $PACKAGE_NAME;
      
      $this->_packages[] = $package_data;
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