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
require_once(dirname(__FILE__) . '/base_package_file_resolver_test.class.php');
require_once(LIMB_DIR . '/class/core/file_resolvers/datasource_file_resolver.class.php');

class datasource_file_resolver_test extends base_package_file_resolver_test
{
  function & _define_resolver()
  {
    return new datasource_file_resolver(new package_file_resolver());
  }

  function test_resolve_datasource_file_from_limb()
  {    
    $this->assertEqual($this->resolver->resolve('fetch_datasource'), LIMB_DIR . '/class/datasources/fetch_datasource.class.php');
  }  
    
  function test_resolve_datasource_file_ok()
  {    
    $this->assertEqual($this->resolver->resolve('test_datasource'), TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/datasources/test_datasource.class.php');
  }  
  
  function test_resolve_datasource_file_failed()
  {
    try
    {    
      $this->resolver->resolve('no_such_datasource');
      $this->assertTrue(false);
    }
    catch(FileNotFoundException $e)
    {
    }
  }  
}

?>
