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
require_once(dirname(__FILE__) . '/CacheBaseTest.class.php');
require_once(LIMB_DIR . '/core/cache/CacheFilePersister.class.php');
require_once(LIMB_DIR . '/core/system/Fs.class.php');

class CacheFilePersisterTest extends CacheBaseTest
{
  var $cache_dir;

  function CacheFilePersisterTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function &_createPersisterImp()
  {
    return new CacheFilePersister();
  }

  function testCachedDiskFiles()
  {
    $cache = new CacheFilePersister('whatever');
    $cache_dir = $cache->getCacheDir();

    $items = Fs :: ls($cache_dir);
    $this->assertEqual(sizeof($items), 0);

    $cache->put(1, $cache_value = 'value');

    $items = Fs :: ls($cache_dir);
    $this->assertEqual(sizeof($items), 1);

    $this->assertEqual($cache->get(1), $cache_value);

    $cache->flushAll();
    rmdir($cache_dir);
  }

  function testProperSerializing()
  {
    $this->cache->put(1, $obj = new Object());

    $this->assertEqual($obj, $this->cache->get(1));
  }
}

?>