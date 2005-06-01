<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CacheRegistryTest.class.php 1340 2005-05-31 15:01:35Z pachanga $
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/CacheBaseTest.class.php');
require_once(LIMB_DIR . '/core/cache/CacheMemoryPersister.class.php');

class CacheMemoryPersisterTest extends CacheBaseTest
{
  function CacheMemoryPersisterTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function &_createPersisterImp()
  {
    return new CacheMemoryPersister();
  }
}

?>