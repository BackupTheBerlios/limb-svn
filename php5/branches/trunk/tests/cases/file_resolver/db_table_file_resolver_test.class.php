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
require_once(LIMB_DIR . '/class/core/file_resolvers/db_table_file_resolver.class.php');

class db_table_file_resolver_test extends base_package_file_resolver_test
{
  function & _define_resolver()
  {
    return new db_table_file_resolver();
  }  

  function test_resolve_db_table_file_from_limb()
  {    
    $this->assertEqual($this->resolver->resolve('content_object'), LIMB_DIR . '/class/db_tables/content_object_db_table.class.php');
  }  
    
  function test_resolve_db_table_file_ok()
  {    
    $this->assertEqual($this->resolver->resolve('test'), TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/db_tables/test_db_table.class.php');
  }  
  
  function test_resolve_db_table_file_failed()
  {
    debug_mock :: expect_write_error('db_table not found', array('class_path' => 'no_such_db_table'));    
    $this->assertFalse($this->resolver->resolve('no_such_db_table'));
  }  
  
}

?>