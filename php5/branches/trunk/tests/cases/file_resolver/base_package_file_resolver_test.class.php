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
define('TEST_PACKAGES_RESOLVER_DIR', dirname(__FILE__) . '/packages/');

SimpleTestOptions::ignore('base_package_file_resolver_test');

class base_package_file_resolver_test extends LimbTestCase
{
  var $resolver;
  
  function & _define_resolver()
  {
    die('abstract method: define file resolver!');    
  }
  
  function setUp()
  {
    debug_mock :: init($this); 
    
    $this->resolver =& $this->_define_resolver();
    
    register_testing_ini(
      'packages.ini',
      ' 
       packages[] = {TEST_PACKAGES_RESOLVER_DIR}package2/1.0/
       packages[] = {TEST_PACKAGES_RESOLVER_DIR}package3/1.1/
      '
    );
    
  }
  
  function tearDown()
  {
    debug_mock :: tally();
    
    unset($this->resolver);
    
    clear_testing_ini(); 
    
    $info =& packages_info :: instance();
    $info->reset();             
  }  

}

?>