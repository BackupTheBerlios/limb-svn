<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'class/lib/util/ini.class.php');

class	db_factory
{
	function &instance($db_type='', $db_params=array(), $force_new_instance=false)
	{
		if(!$db_type)
			$db_type = get_ini_option('common.ini', 'type', 'DB');
		elseif(!$db_type)
	    $db_type = 'null';
	  
		$db_class_name = 'db_' . $db_type;

		$obj	=& $GLOBALS['global_db_handler'];
		
		if (get_class( $obj ) != $db_class_name || $force_new_instance)
		{
			if(!$db_params && $db_type !== 'null')
			{
				$db_params['host'] = get_ini_option('common.ini', 'host', 'DB');
				$db_params['login'] = get_ini_option('common.ini', 'login', 'DB');
				$db_params['password'] = get_ini_option('common.ini', 'password', 'DB');
				$db_params['name'] = get_ini_option('common.ini', 'name', 'DB');
			}
			
		  include_once(LIMB_DIR . 'class/lib/db/' . $db_class_name . '.class.php');
		  
		  $obj =& new $db_class_name($db_params);
		  
  		$GLOBALS['global_db_handler'] =& $obj;
		}
		return $obj;
	}
	
	function select_db($db_name)
	{
		$db =& db_factory :: instance();
		$db->select_db($db_name);
	}

	function create($db_type, $db_params)
	{	
		$db_class_name = 'db_' . $db_type;

	  include_once(LIMB_DIR . 'class/lib/db/' . $db_class_name . '.class.php');
	  
	  return new $db_class_name($db_params);
	}
}

function start_user_transaction()
{
	$db =& db_factory :: instance();
	$db->begin();
}

function commit_user_transaction()
{
	$db =& db_factory :: instance();
	$db->commit();
}

function rollback_user_transaction()
{
	$db =& db_factory :: instance();
	$db->rollback();
}
?>