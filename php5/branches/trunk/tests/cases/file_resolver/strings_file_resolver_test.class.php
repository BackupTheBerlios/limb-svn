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
    try
    {    
      $this->resolver->resolve('no_such_strings_file', 'fr');
      $this->assertTrue(false);
    }
    catch(FileNotFoundException $e)
    {
      $this->assertEqual($e->getAdditionalParams(),       
        array(
          'locale_id' => 'fr',
  		    'file_path' => 'no_such_strings_file',
        )
      );
    }    
  } 
  
}

?>