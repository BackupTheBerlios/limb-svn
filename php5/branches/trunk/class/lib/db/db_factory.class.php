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

class	db_factory
{
  static public function instance($db_type='', $db_params=array(), $force_new_instance=false)
  {
    if(!$db_type)
      $db_type = get_ini_option('common.ini', 'type', 'DB');
    elseif(!$db_type)
      $db_type = 'null';

    $db_class_name = 'db_' . $db_type;

    $obj = null;
    if (isset($GLOBALS['global_db_handler']))
      $obj =& $GLOBALS['global_db_handler'];

    if (get_class($obj) != $db_class_name || $force_new_instance)
    {
      if(!$db_params && $db_type !== 'null')
      {
        $conf = get_ini('common.ini');
        $db_params['host'] = $conf->get_option('host', 'DB');
        $db_params['login'] = $conf->get_option('login', 'DB');
        $db_params['password'] = $conf->get_option('password', 'DB');
        $db_params['name'] = $conf->get_option('name', 'DB');
      }

      include_once(LIMB_DIR . '/class/lib/db/' . $db_class_name . '.class.php');

      $obj = new $db_class_name($db_params);

      $GLOBALS['global_db_handler'] = $obj;
    }
    return $obj;
  }

  public function select_db($db_name)
  {
    db_factory :: instance()->select_db($db_name);
  }

  public function create($db_type, $db_params)
  {
    $db_class_name = 'db_' . $db_type;

    include_once(LIMB_DIR . '/class/lib/db/' . $db_class_name . '.class.php');

    return new $db_class_name($db_params);
  }
}

function start_user_transaction()
{
  db_factory :: instance()->begin();
}

function commit_user_transaction()
{
  db_factory :: instance()->commit();
}

function rollback_user_transaction()
{
  db_factory :: instance()->rollback();
}
?>