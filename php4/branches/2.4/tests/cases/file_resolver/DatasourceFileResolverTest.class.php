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
require_once(LIMB_DIR . '/class/file_resolvers/DatasourceFileResolver.class.php');

class DatasourceFileResolverTest extends BasePackageFileResolverTest
{
  function & _defineResolver()
  {
    return new DatasourceFileResolver(new PackageFileResolver());
  }

  function testResolveDatasourceFileFromLimb()
  {
    $this->assertEqual($this->resolver->resolve('SiteObjectsDatasource'),
                       LIMB_DIR . '/class/datasources/SiteObjectsDatasource.class.php');
  }

  function testResolveDatasourceFileOk()
  {
    $this->assertEqual($this->resolver->resolve('TestDatasource'),
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/datasources/TestDatasource.class.php');
  }

  function testResolveDatasourceFileFailed()
  {
    $this->resolver->resolve('no_such_datasource');
    $this->assertTrue(catch('Exception', $e));
  }
}

?>
