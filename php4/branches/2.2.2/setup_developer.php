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
define('DEVELOPER_ENVIROMENT', true);

require_once(LIMB_DIR . '/core/lib/debug/debug.class.php');

if(!defined('ERROR_HANDLER_TYPE') && isset($_SERVER['SERVER_PORT']))
	if($_SERVER['SERVER_PORT'] == 81)
		define('ERROR_HANDLER_TYPE', DEBUG_HANDLE_NATIVE);

if(!defined('MEDIA_DIR'))
	define('MEDIA_DIR', '\\\\pinggy\\media\\'. $matches[1] .'\\');			

?>