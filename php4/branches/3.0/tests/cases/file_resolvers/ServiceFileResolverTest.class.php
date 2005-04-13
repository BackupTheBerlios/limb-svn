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
require_once(LIMB_DIR . '/core/file_resolvers/ServiceFileResolver.class.php');

class ServiceFileResolverTest extends BasePackageFileResolverTest
{
  function ServiceFileResolverTest()
  {
    parent :: BasePackageFileResolverTest(__FILE__);
  }

  function & _defineResolver()
  {
    return new ServiceFileResolver(new PackageFileResolver());
  }

  function testResolveServiceFileInPackagesOk()
  {
    $this->assertEqual($this->resolver->resolve('test.service.ini'),
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/services/test.service.ini');
  }

  function testResolveServiceFileFailed()
  {
    $this->resolver->resolve('no_such_service');
    $this->assertTrue(catch('Exception', $e));
  }

}

?>