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
require_once(LIMB_DIR . '/class/core/file_resolvers/strings_file_resolver.class.php');

class strings_file_resolver_test extends base_package_file_resolver_test
{
  function & _define_resolver()
  {
    return new strings_file_resolver(new package_file_resolver());
  }
  
  function test_resolve_strings_file_ok()
  { 
    $this->assertEqual($this->resolver->resolve('test', array('en')), TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/i18n/test_en.ini');
  }  
  
  function test_resolve_strings_file_failed()
  {
    try
    { 
      $this->resolver->resolve('no_such_strings_file', array('fr'));
      $this->assertTrue(false);
    }
    catch(FileNotFoundException $e)
    {
      $this->assertEqual($e->getAdditionalParams(),       
        array(
  		    'file_path' => 'i18n/no_such_strings_file_fr.ini',
        )
      );
    }    
  } 
  
}

?>