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

class	db_factory
{
	function &instance($db_type='', $db_params='', $force_new_instance=false)
	{	
		if(!$db_type && defined('DB_TYPE'))
			$db_type = DB_TYPE;
	  elseif(!$db_type)
	    $db_type = 'fake';
		
		$db_class_name = 'db_' . $db_type;

		$obj	=& $GLOBALS['global_db_handler'];
		
		if (get_class( $obj ) != $db_class_name || $force_new_instance)
		{
			if(!$db_params)
			{
				$db_params['host'] = DB_HOST;
				$db_params['login'] = DB_LOGIN;
				$db_params['password'] = DB_PASSWORD;
				$db_params['name'] = DB_NAME;
			}
			
		  include_once(LIMB_DIR . 'core/lib/db/' . $db_class_name . '.class.php');
		  
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

	  include_once(LIMB_DIR . 'core/lib/db/' . $db_class_name . '.class.php');
	  
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