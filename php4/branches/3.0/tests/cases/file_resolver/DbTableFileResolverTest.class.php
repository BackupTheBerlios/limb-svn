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
require_once(LIMB_DIR . '/core/file_resolvers/DbTableFileResolver.class.php');

class DbTableFileResolverTest extends BasePackageFileResolverTest
{
  function & _defineResolver()
  {
    return new DbTableFileResolver(new PackageFileResolver());
  }

  function testResolveDbTableFileFromLimb()
  {
    $this->assertEqual($this->resolver->resolve('Empty'),
                       LIMB_DIR . '/core/db_tables/EmptyDbTable.class.php');
  }

  function testResolveDbTableFileOk()
  {
    $this->assertEqual($this->resolver->resolve('Test'),
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/db_tables/TestDbTable.class.php');
  }

  function testResolveDbTableFileFailed()
  {
    $this->resolver->resolve('no_such_db_table');
    $this->assertTrue(catch('Exception', $e));
  }

}

?>