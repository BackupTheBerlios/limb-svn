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

Mock::generatePartial(
  'package_file_resolver', 
  'package_file_resolver_test_version', 
  array('_find_file_in_packages')
);

class package_file_resolver_test extends base_package_file_resolver_test
{
  function & _define_resolver()
  {
    return new package_file_resolver();
  }
  
  function test_resolve_file_name()
  {    
    $this->assertEqual($this->resolver->resolve('package2_action'), TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/package2_action');
  }

}

?>