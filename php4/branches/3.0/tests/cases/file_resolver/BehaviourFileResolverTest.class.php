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
require_once(LIMB_DIR . '/core/file_resolvers/BehaviourFileResolver.class.php');

class BehaviourFileResolverTest extends BasePackageFileResolverTest
{
  function & _defineResolver()
  {
    return new BehaviourFileResolver(new PackageFileResolver());
  }

  function testResolveBehaviourFileInPackagesOk()
  {
    $this->assertEqual($this->resolver->resolve('TestBehaviour'),
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/behaviours/TestBehaviour.class.php');
  }

  function testResolveBehaviourFileFailed()
  {
    $this->resolver->resolve('no_such_behaviour');
    $this->assertTrue(catch('Exception', $e));
  }

}

?>