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
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');//for conevenience

class LimbDbPool
{
  var $conns = array();

  function & newConnection(&$conf)
  {
    $driver = $conf->get('driver');
    $class = ucfirst($driver) . 'Connection';

    include_once(WACT_ROOT . '/db/drivers/' . $driver . '/driver.inc.php');
    $connection = new $class($conf);
    $connection->connect();

    return $connection;
  }

  function & getConnection(&$conf)
  {
    $name = $conf->get('name');

    if(!isset($this->conns[$name]))
      $this->conns[$name] =& $this->newConnection($conf);

    return $this->conns[$name];
  }
}

?>