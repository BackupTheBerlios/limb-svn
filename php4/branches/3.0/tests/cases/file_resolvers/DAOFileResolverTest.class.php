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
require_once(LIMB_DIR . '/core/file_resolvers/DAOFileResolver.class.php');

class DAOFileResolverTest extends BasePackageFileResolverTest
{
  function & _defineResolver()
  {
    return new DAOFileResolver(new PackageFileResolver());
  }

  function testResolveDAOFileFromLimb()
  {
    $this->assertEqual($this->resolver->resolve('DAO'),
                       LIMB_DIR . '/core/DAO/DAO.class.php');
  }

  function testResolveDAOFileOk()
  {
    $this->assertEqual($this->resolver->resolve('TestDAO'),
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/DAO/TestDAO.class.php');
  }

  function testResolveDAOFileFailed()
  {
    $this->resolver->resolve('no_such_dao');
    $this->assertTrue(catch('Exception', $e));
  }
}

?>
