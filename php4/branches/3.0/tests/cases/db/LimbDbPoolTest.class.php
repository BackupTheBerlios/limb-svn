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
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/core/db/DbConnectionConfig.class.php');
require_once(LIMB_DIR . '/core/util/ini_support.inc.php');

Mock :: generate('DbConnectionConfig');

class LimbDbPoolTest extends LimbTestCase
{
  function LimbDbPoolTest()
  {
    parent :: LimbTestCase('db pool test case');
  }

  function testGetConnection()
  {
    $ini =& getIni('common.ini');

    $conf = new MockDbConnectionConfig($this);

    $conf->expectCallCount('get', 7);

    $conf->setReturnValue('get', 'test', array('name'));
    $conf->setReturnValue('get', $ini->getOption('driver', 'DB'), array('driver'));
    $conf->setReturnValue('get', $ini->getOption('host', 'DB'), array('host'));
    $conf->setReturnValue('get', $ini->getOption('database', 'DB'), array('database'));
    $conf->setReturnValue('get', $ini->getOption('user', 'DB'), array('user'));
    $conf->setReturnValue('get', $ini->getOption('password', 'DB'), array('password'));

    $pool = new LimbDbPool();

    $conn1 = $pool->getConnection($conf);
    $conn2 = $pool->getConnection($conf);

    $this->assertTrue($conn1 === $conn2);

    $conf->tally();
  }
}
?>