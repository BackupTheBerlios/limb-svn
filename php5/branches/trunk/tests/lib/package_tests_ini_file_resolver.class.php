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
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolver.interface.php');

class package_tests_ini_file_resolver implements file_resolver
{
  var $package_directory;
  
  function package_tests_ini_file_resolver($package_directory)
  {
    $this->package_directory = $package_directory;
  }
  
  function resolve($file_name)
  {
  	if (file_exists($this->package_directory . '/tests/settings/' . $file_name))
  		$dir = $this->package_directory . '/tests/settings/';
  	elseif (file_exists(LIMB_DIR . '/settings/' . $file_name))
  		$dir = LIMB_DIR . '/settings/';
  	else
      throw new FileNotFoundException('ini file not found', $file_name);
		  
		return $dir . $file_name;
  }
}

?>