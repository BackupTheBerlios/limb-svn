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
class LimbDbConnectionConfiguration
{
  function get($name)
  {
    include_once(LIMB_DIR . '/class/lib/util/ini_support.inc.php');
    return getIniOption('common.ini', $name, 'DB');
  }
}

class LimbDbPool
{
  function & newConnection($name, $conf = null)
  {
    if($conf === null)
      $conf =& new LimbDbConnectionConfiguration();

    $driver = $conf->get('driver');
    $class = ucfirst($driver) . 'Connection';

    include_once(WACT_ROOT . 'db/drivers/' . $driver . '/driver.inc.php');
    $connection = new $class($conf);
    $connection->connect();

    return $connection;
  }

  function & getConnection($name = 'default', $conf = null)
  {
    if (!isset($GLOBALS['DatabaseConnectionObjList'][$name]))
    {
      $GLOBALS['DatabaseConnectionObjList'][$name] =& LimbDbPool :: newConnection($name, $conf);
    }
    return $GLOBALS['DatabaseConnectionObjList'][$name];
  }
}

?>