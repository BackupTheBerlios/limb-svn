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
require_once(LIMB_DIR . '/class/lib/util/ini_support.inc.php');

class	DbFactory
{
  static public function instance($db_type='', $db_params=array(), $force_new_instance=false)
  {
    if(!$db_type)
      $db_type = getIniOption('common.ini', 'type', 'DB');
    elseif(!$db_type)
      $db_type = 'null';

    $db_class_name = self :: _mapTypeToClass($db_type);

    $obj = null;
    if (isset($GLOBALS['global_db_handler']))
      $obj =& $GLOBALS['global_db_handler'];

    if (get_class($obj) != $db_class_name ||  $force_new_instance)
    {
      if(!$db_params &&  $db_type !== 'null')
      {
        $conf = getIni('common.ini');
        $db_params['host'] = $conf->getOption('host', 'DB');
        $db_params['login'] = $conf->getOption('login', 'DB');
        $db_params['password'] = $conf->getOption('password', 'DB');
        $db_params['name'] = $conf->getOption('name', 'DB');
      }

      include_once(LIMB_DIR . '/class/lib/db/' . $db_class_name . '.class.php');

      $obj = new $db_class_name($db_params);

      $GLOBALS['global_db_handler'] = $obj;
    }
    return $obj;
  }

  public function selectDb($db_name)
  {
    DbFactory :: instance()->selectDb($db_name);
  }

  public function create($db_type, $db_params)
  {
    $db_class_name = self :: _mapTypeToClass($db_type);

    include_once(LIMB_DIR . '/class/lib/db/' . $db_class_name . '.class.php');

    return new $db_class_name($db_params);
  }

  static protected function _mapTypeToClass($type)
  {
    return 'Db' . ucfirst($type);
  }
}

function startUserTransaction()
{
  DbFactory :: instance()->begin();
}

function commitUserTransaction()
{
  DbFactory :: instance()->commit();
}

function rollbackUserTransaction()
{
  DbFactory :: instance()->rollback();
}
?>