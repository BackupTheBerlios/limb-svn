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
require_once(LIMB_DIR . '/class/core/file_resolvers/data_mapper_file_resolver.class.php');

class data_mapper_file_resolver_test extends base_package_file_resolver_test
{
  function & _define_resolver()
  {
    return new data_mapper_file_resolver(new package_file_resolver());
  }  
      
  function test_resolve_data_mapper_file_ok()
  {    
    $this->assertEqual($this->resolver->resolve('test_mapper'), 
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/data_mappers/test_mapper.class.php');
  }  
  
  function test_resolve_data_mapper_file_failed()
  {
    try
    {    
      $this->resolver->resolve('no_such_mapper');
      $this->assertTrue(false);
    }
    catch(FileNotFoundException $e){}
  }  
  
}

?>