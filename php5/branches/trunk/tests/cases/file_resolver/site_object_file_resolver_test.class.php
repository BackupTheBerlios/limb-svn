<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/base_package_file_resolver_test.class.php');
require_once(LIMB_DIR . '/class/core/file_resolvers/site_object_file_resolver.class.php');

class site_object_file_resolver_test extends base_package_file_resolver_test
{
  function & _define_resolver()
  {
    return new site_object_file_resolver(new package_file_resolver());
  }

  function test_resolve_site_object_file_ok()
  {
    $this->assertEqual($this->resolver->resolve('test_site_object'), TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/site_objects/test_site_object.class.php');
  }

  function test_resolve_site_object_file_failed()
  {
    try
    {
      $this->resolver->resolve('no_such_site_object');
      $this->assertTrue(false);
    }
    catch(FileNotFoundException $e)
    {
    }
  }

}

?>