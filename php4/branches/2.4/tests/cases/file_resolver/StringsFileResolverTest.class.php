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
require_once(LIMB_DIR . '/class/core/file_resolvers/StringsFileResolver.class.php');

class StringsFileResolverTest extends BasePackageFileResolverTest
{
  function & _defineResolver()
  {
    return new StringsFileResolver(new PackageFileResolver());
  }

  function testResolveStringsFileOk()
  {
    $this->assertEqual($this->resolver->resolve('test', array('en')),
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/i18n/test_en.ini');
  }

  function testResolveStringsFileFailed()
  {
    try
    {
      $this->resolver->resolve('no_such_strings_file', array('fr'));
      $this->assertTrue(false);
    }
    catch(FileNotFoundException $e)
    {
      $this->assertEqual($e->getAdditionalParams(),
        array(
          'file_path' => 'i18n/no_such_strings_file_fr.ini',
        )
      );
    }
  }

}

?>