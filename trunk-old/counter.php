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
require_once('setup.php');
require_once(LIMB_DIR . 'core/lib/http/http_request.inc.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

function get_counter_record()
{
	$connection = & db_factory :: get_connection();
	$connection->sql_select('sys_stat_counter', '*');
	return $connection->fetch_row();
}

function register_client_info()
{
	//collecting js client specific stuff
	$script_language = isset($_REQUEST['sl']) ? $_REQUEST['sl'] : '?';
	$cookie_enabled = isset($_REQUEST['c']) ? $_REQUEST['c'] : '?';
	$java_enabled = isset($_REQUEST['j']) ? $_REQUEST['j'] : '?';
	$screen_size = isset($_REQUEST['wh']) ? $_REQUEST['wh'] : '?';
	$screen_depth = isset($_REQUEST['px']) ? $_REQUEST['px'] : '?';
	$time_offset = isset($_REQUEST['t']) ? $_REQUEST['t'] : '?';
	
	//....
}
						
?>