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
require_once(LIMB_DIR . '/class/core/file_resolvers/FinderFileResolver.class.php');

class FinderFileResolverTest extends BasePackageFileResolverTest
{
  function & _defineResolver()
  {
    return new FinderFileResolver(new PackageFileResolver());
  }

  function testResolveFinderFileOk()
  {
    $this->assertEqual($this->resolver->resolve('TestFinder'),
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/finders/TestFinder.class.php');
  }

  function testResolveFinderFileFailed()
  {
    $this->resolver->resolve('no_such_finder');
    $this->assertTrue(catch('Exception', $e));
  }

}

?>