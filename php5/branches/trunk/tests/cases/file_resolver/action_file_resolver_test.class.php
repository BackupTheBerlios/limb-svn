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
require_once(dirname(__FILE__) . '/base_package_file_resolver_test.class.php');
require_once(LIMB_DIR . '/class/core/file_resolvers/action_file_resolver.class.php');

class action_file_resolver_test extends base_package_file_resolver_test
{  
  function & _define_resolver()
  {
    return new action_file_resolver();
  }  
  
  function test_resolve_action_file_in_limb()
  { 
    $this->assertEqual($this->resolver->resolve('action'), LIMB_DIR . 'class/core/actions/action.class.php');
  }  
    
  function test_resolve_action_file_ok()
  {    
    $this->assertEqual($this->resolver->resolve('test_action'), TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/actions/test_action.class.php');
  }  
  
  function test_resolve_action_file_failed()
  {
    try
    {    
      $this->resolver->resolve('no_such_action');
      $this->assertTrue(false);
    }
    catch(FileNotFoundException $e)
    {
    }
  }  
  
}

?>