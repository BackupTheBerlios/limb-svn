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
require_once(dirname(__FILE__) . '/BasePackageFileResolverTest.class.php');
require_once(LIMB_DIR . '/core/file_resolvers/SiteObjectFileResolver.class.php');

class SiteObjectFileResolverTest extends BasePackageFileResolverTest
{
  function & _defineResolver()
  {
    return new SiteObjectFileResolver(new PackageFileResolver());
  }

  function testResolveSiteObjectFileOk()
  {
    $this->assertEqual($this->resolver->resolve('TestSiteObject'),
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/site_objects/TestSiteObject.class.php');
  }

  function testResolveSiteObjectFileFailed()
  {
    $this->resolver->resolve('no_such_site_object');
    $this->assertTrue(catch('Exception', $e));
  }

}

?>