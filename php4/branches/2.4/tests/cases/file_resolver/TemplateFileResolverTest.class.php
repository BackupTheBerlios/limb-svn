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
define('OVERRIDE_TEMPLATE_DIR_FOR_TEST', dirname(__FILE__) . '/design/');
require_once(dirname(__FILE__) . '/BasePackageFileResolverTest.class.php');
require_once(LIMB_DIR . '/class/core/file_resolvers/TemplateFileResolver.class.php');

Mock :: generatePartial(
  'TemplateFileResolver',
  'TemplateFileResolverTestVersion',
  array('_getLocalePrefix')
);

class TemplateFileResolverTest extends BasePackageFileResolverTest
{
  function & _defineResolver()
  {
    $resolver = new TemplateFileResolverTestVersion($this);
    $resolver->TemplateFileResolver(new PackageFileResolver());

    $resolver->setReturnValue('_getLocalePrefix', '');

    return $resolver;
  }

  function setUp()
  {
    parent :: setUp();

    registerTestingIni(
      'common.ini',
      '
      [Templates]
       path = ' . OVERRIDE_TEMPLATE_DIR_FOR_TEST . '
      '
    );
  }

  function tearDown()
  {
    $this->resolver->tally();
    parent :: tearDown();
  }

  function testResolveTemplateFileFoundInTemplatesDirUsingLocale()
  {
    $this->resolver->setReturnValueAt(0, '_getLocalePrefix', '_en/');
    $this->assertEqual($this->resolver->resolve('test1.html'),
                       OVERRIDE_TEMPLATE_DIR_FOR_TEST . '_en/test1.html');
  }

  function testResolveTemplateFileFoundInTemplatesDir()
  {
    $this->assertEqual($this->resolver->resolve('test1.html'),
                       OVERRIDE_TEMPLATE_DIR_FOR_TEST . 'test1.html');
  }

  function testResolveTemplateFileFoundInPackageUsingLocale()
  {
    $this->resolver->setReturnValueAt(0, '_getLocalePrefix', '_en/');
    $this->assertEqual($this->resolver->resolve('test2.html'),
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/design/_en/test2.html');
  }

  function testResolveTemplateFileFoundInPackage()
  {
    $this->assertEqual($this->resolver->resolve('test2.html'),
                       TEST_PACKAGES_RESOLVER_DIR . 'package2/1.0/design/test2.html');
  }

  function testResolveTemplateFileFailed()
  {
    try
    {
      $this->resolver->resolve('no_such_template.html');
      $this->assertTrue(false);
    }
    catch(FileNotFoundException $e)
    {
    }
  }

}

?>