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
require_once(LIMB_DIR . '/class/core/file_resolvers/controller_file_resolver.class.php');

class controller_file_resolver_test extends base_package_file_resolver_test
{
  function & _define_resolver()
  {
    return new controller_file_resolver();
  }
  
  function test_resolve_controller_file_ok()
  {
    $this->assertEqual($this->resolver->resolve('test_controller'), TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/controllers/test_controller.class.php');
  }  
  
  function test_resolve_controller_file_failed()
  {
    try
    {    
      $this->resolver->resolve('no_such_controller');
      $this->assertTrue(false);
    }
    catch(FileNotFoundException $e)
    {
    }    
  }  
  
}

?>