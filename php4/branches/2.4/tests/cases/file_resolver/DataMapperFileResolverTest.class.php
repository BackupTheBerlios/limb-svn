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
require_once(LIMB_DIR . '/class/core/file_resolvers/DataMapperFileResolver.class.php');

class DataMapperFileResolverTest extends BasePackageFileResolverTest
{
  function & _defineResolver()
  {
    return new DataMapperFileResolver(new PackageFileResolver());
  }

  function testResolveDataMapperFileOk()
  {
    $this->assertEqual($this->resolver->resolve('TestMapper'),
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/data_mappers/TestMapper.class.php');
  }

  function testResolveDataMapperFileFailed()
  {
    $this->assertTrue(Limb :: isError($this->resolver->resolve('no_such_mapper')));
  }

}

?>