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
define('OVERRIDE_TEMPLATE_DIR_FOR_TEST', dirname(__FILE__) . '/design/');
require_once(dirname(__FILE__) . '/base_package_file_resolver_test.class.php');
require_once(LIMB_DIR . '/class/core/file_resolvers/template_file_resolver.class.php');

Mock :: generatePartial(
  'template_file_resolver', 
  'template_file_resolver_test_version', 
  array('_get_locale_prefix') 
); 

class template_file_resolver_test extends base_package_file_resolver_test
{
  function & _define_resolver()
  {
    $resolver = new template_file_resolver_test_version($this);
    $resolver->__construct(new package_file_resolver());
    
    $resolver->setReturnValue('_get_locale_prefix', '');
  
    return $resolver;
  }

  function setUp()
  {
    parent :: setUp();
    
    register_testing_ini(
      'common.ini',
      ' 
      [Templates]
       path = ' . OVERRIDE_TEMPLATE_DIR_FOR_TEST . '
      '
    );    
  }
  
  function tearDown()
  {
    $this->resolver->tally();
    parent :: tearDown();
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
    $this->assertEqual($this->resolver->resolve('test2.html'), TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/design/_en/test2.html');
  }  
    
  function test_resolve_template_file_found_in_package()
  {    
    $this->assertEqual($this->resolver->resolve('test2.html'), TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/design/test2.html');
  }  
  
  function test_resolve_template_file_failed()
  {
    try
    {    
      $this->resolver->resolve('no_such_template.html');
      $this->assertTrue(false);
    }
    catch(FileNotFoundException $e)
    {
    }
  }  
  
}

?>