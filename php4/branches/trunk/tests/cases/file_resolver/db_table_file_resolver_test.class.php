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
define('TEST_DB_TABLE_RESOLVER_PACKAGE_DIR', dirname(__FILE__) . '/packages/common/');

require_once(LIMB_DIR . '/class/core/file_resolvers/db_table_file_resolver.class.php');

class db_table_file_resolver_test extends LimbTestCase
{
  var $resolver;
  
  function setUp()
  {
    debug_mock :: init($this); 
    
    register_testing_ini(
      'packages.ini',
      ' 
      [package-1]
       path = {TEST_DB_TABLE_RESOLVER_PACKAGE_DIR}package1/
      [package-2]
       path = {TEST_DB_TABLE_RESOLVER_PACKAGE_DIR}package2/1.0/       
      [package-3]
       path = {TEST_DB_TABLE_RESOLVER_PACKAGE_DIR}package3/1.1/
      '
    );
  
    $this->resolver =& new db_table_file_resolver();
  }
  
  function tearDown()
  {
    debug_mock :: tally();
    unset($this->resolver);
    clear_testing_ini();    
  }
    
  function test_resolve_db_table_file_ok()
  {    
    $this->assertEqual($this->resolver->resolve('test'), TEST_DB_TABLE_RESOLVER_PACKAGE_DIR . 'package2/1.0/db_tables/test_db_table.class.php');
  }  
  
  function test_resolve_db_table_file_failed()
  {
    debug_mock :: expect_write_error('db_table not found', array('class_path' => 'no_such_db_table'));    
    $this->assertFalse($this->resolver->resolve('no_such_db_table'));
  }  
  
}

?>