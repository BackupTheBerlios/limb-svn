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
require_once(LIMB_DIR . '/core/file_resolvers/PackageFileResolver.class.php');

Mock :: generatePartial(
  'PackageFileResolver',
  'PackageFileResolverTestVersion',
  array('_findFileInPackages')
);

class PackageFileResolverTest extends BasePackageFileResolverTest
{
  function PackageFileResolverTest()
  {
    parent :: BasePackageFileResolverTest(__FILE__);
  }

  function & _defineResolver()
  {
    return new PackageFileResolver();
  }

  function testResolveFileName()
  {
    $this->assertEqual($this->resolver->resolve('package2_action'),
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/package2_action');
  }

}

?>