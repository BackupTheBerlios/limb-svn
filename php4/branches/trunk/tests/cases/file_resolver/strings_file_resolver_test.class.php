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
require_once(LIMB_DIR . '/class/core/file_resolvers/strings_file_resolver.class.php');

class strings_file_resolver_test extends base_package_file_resolver_test
{
  function & _define_resolver()
  {
    return new strings_file_resolver();
  }
  
  function test_resolve_strings_file_ok()
  {    
    $this->assertEqual($this->resolver->resolve('test', 'en'), TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/i18n/test_en.ini');
  }  
  
  function test_resolve_strings_file_failed()
  {
    debug_mock :: expect_write_error('strings file not found', 
      array(
  		  'file_name' => 'no_such_strings_file',
  		  'locale_id' => 'fr'
      )
    );
    
    $this->assertFalse($this->resolver->resolve('no_such_strings_file', 'fr'));
  } 
  
}

?>