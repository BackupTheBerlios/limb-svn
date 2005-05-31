<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CacheRegistryTest.class.php 1336 2005-05-30 12:54:56Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/cache/CacheFilePersister.class.php');

class CacheFilePersisterTest extends LimbTestCase
{
  var $cache;

  function CacheFilePersisterTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->cache = new CacheFilePersister();
    $this->cache->flushAll();
  }

  function testProperSerializing()
  {
    $this->cache->put(1, $obj = new Object());
    $this->cache->assign($var, 1);

    $this->assertEqual($obj, $var);
  }
}

?>