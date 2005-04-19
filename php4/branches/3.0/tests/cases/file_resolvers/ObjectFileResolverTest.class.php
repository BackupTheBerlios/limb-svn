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
require_once(LIMB_DIR . '/core/file_resolvers/ObjectFileResolver.class.php');

class ObjectFileResolverTest extends BasePackageFileResolverTest
{
  function ObjectFileResolverTest()
  {
    parent :: BasePackageFileResolverTest(__FILE__);
  }

  function & _defineResolver()
  {
    return new ObjectFileResolver(new PackageFileResolver());
  }

  function testResolveObjectFileOk()
  {
    $this->assertEqual($this->resolver->resolve('TestObject'),
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/TestObject.class.php');
  }

  function testResolveObjectFileFailed()
  {
    $this->resolver->resolve('no_such_object');
    $this->assertTrue(catch_error('LimbException', $e));
  }

}

?>