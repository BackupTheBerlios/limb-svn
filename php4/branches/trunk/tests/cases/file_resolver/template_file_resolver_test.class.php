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
define('TEST_TEMPLATE_RESOLVER_PACKAGE_DIR', dirname(__FILE__) . '/packages/common/');
define('OVERRIDE_TEMPLATE_DIR_FOR_TEST', dirname(__FILE__) . '/design/');

require_once(LIMB_DIR . '/class/core/file_resolvers/template_file_resolver.class.php');

Mock::generatePartial(
  'template_file_resolver', 
  'template_file_resolver_test_version', 
  array('_get_locale_prefix') 
); 

class template_file_resolver_test extends LimbTestCase
{
  var $resolver;
  
  function setUp()
  {
    debug_mock :: init($this); 
    
    register_testing_ini(
      'packages.ini',
      ' 
      [package_1]
       path = {TEST_TEMPLATE_RESOLVER_PACKAGE_DIR}package1/
      [package_2]
       path = {TEST_TEMPLATE_RESOLVER_PACKAGE_DIR}package2/1.0/       
      [package_3]
       path = {TEST_TEMPLATE_RESOLVER_PACKAGE_DIR}package3/1.1/
      '
    );
    
    register_testing_ini(
      'common.ini',
      ' 
      [Templates]
       path = ' . OVERRIDE_TEMPLATE_DIR_FOR_TEST . '
      '
    );    
  
    $this->resolver =& new template_file_resolver_test_version($this);
    $this->resolver->template_file_resolver();
    
    $this->resolver->setReturnValue('_get_locale_prefix', '');
  }
  
  function tearDown()
  {
    debug_mock :: tally();
    
    $this->resolver->tally();
    
    unset($this->resolver);
    clear_testing_ini();    
  }

  function test_resolve_template_file_found_in_templates_dir_using_locale()
  {
    $this->resolver->setReturnValueAt(0, '_get_locale_prefix', '_en/');
    $this->assertEqual($this->resolver->resolve('test1.html'), OVERRIDE_TEMPLATE_DIR_FOR_TEST . '_en/test1.html');
  }  

  function test_resolve_template_file_found_in_templates_dir()
  {      
    $this->assertEqual($this->resolver->resolve('test1.html'), OVERRIDE_TEMPLATE_DIR_FOR_TEST . 'test1.html');
  }  

  function test_resolve_template_file_found_in_package_using_locale()
  {
    $this->resolver->setReturnValueAt(0, '_get_locale_prefix', '_en/');
    $this->assertEqual($this->resolver->resolve('test2.html'), TEST_TEMPLATE_RESOLVER_PACKAGE_DIR . 'package2/1.0/design/_en/test2.html');
  }  
    
  function test_resolve_template_file_found_in_package()
  {    
    $this->assertEqual($this->resolver->resolve('test2.html'), TEST_TEMPLATE_RESOLVER_PACKAGE_DIR . 'package2/1.0/design/test2.html');
  }  
  
  function test_resolve_template_file_failed()
  {
    debug_mock :: expect_write_error('template not found', array('file_path' => 'no_such_template.html'));    
    $this->assertFalse($this->resolver->resolve('no_such_template.html'));
  }  
  
}

?>