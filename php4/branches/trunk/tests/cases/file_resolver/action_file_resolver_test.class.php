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
define('TEST_ACTION_RESOLVER_PACKAGE_DIR', dirname(__FILE__) . '/packages/common/');

require_once(LIMB_DIR . '/class/core/file_resolvers/action_file_resolver.class.php');

class action_file_resolver_test extends LimbTestCase
{
  var $resolver;
  
  function setUp()
  {
    debug_mock :: init($this); 
    
    register_testing_ini(
      'packages.ini',
      ' 
      [package_1]
       path = {TEST_ACTION_RESOLVER_PACKAGE_DIR}package1/
      [package_2]
       path = {TEST_ACTION_RESOLVER_PACKAGE_DIR}package2/1.0/       
      [package_3]
       path = {TEST_ACTION_RESOLVER_PACKAGE_DIR}package3/1.1/
      '
    );
  
    $this->resolver =& new action_file_resolver();
  }
  
  function tearDown()
  {
    debug_mock :: tally();
    unset($this->resolver);
    clear_testing_ini();    
  }

  function test_resolve_action_file_in_limb()
  {    
    $this->assertEqual($this->resolver->resolve('action'), LIMB_DIR . 'class/core/actions/action.class.php');
  }  
    
  function test_resolve_action_file_ok()
  {    
    $this->assertEqual($this->resolver->resolve('test_action'), TEST_ACTION_RESOLVER_PACKAGE_DIR . 'package2/1.0/actions/test_action.class.php');
  }  
  
  function test_resolve_action_file_failed()
  {
    debug_mock :: expect_write_error('action not found', array('class_path' => 'no_such_action'));    
    $this->assertFalse($this->resolver->resolve('no_such_action'));
  }  
  
}

?>