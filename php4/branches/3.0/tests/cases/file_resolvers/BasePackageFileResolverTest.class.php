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
define('TEST_PACKAGES_RESOLVER_DIR', dirname(__FILE__) . '/packages/');

SimpleTestOptions :: ignore('BasePackageFileResolverTest');

class BasePackageFileResolverTest extends LimbTestCase
{
  var $resolver;

  function & _defineResolver()
  {
    die('abstract method: define file resolver!');
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $toolkit->flushINICache();
    $inst =& PackagesInfo :: instance();
    $inst->reset();

    DebugMock :: init($this);

    $this->resolver =& $this->_defineResolver();

    registerTestingIni(
      'packages.ini',
      '
       packages[] = {TEST_PACKAGES_RESOLVER_DIR}package2/1.0/
       packages[] = {TEST_PACKAGES_RESOLVER_DIR}package3/1.1/
      '
    );
  }

  function tearDown()
  {
    DebugMock :: tally();

    unset($this->resolver);

    clearTestingIni();

    $inst =& PackagesInfo :: instance();
    $inst->reset();
  }

}

?>