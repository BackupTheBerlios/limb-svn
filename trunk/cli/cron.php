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
$site_path = $argv[1];
require_once($site_path . '/setup.php');

require_once(LIMB_DIR . 'core/lib/error/debug.class.php');
require_once(LIMB_DIR . 'core/request/nonbuffered_response.class.php');
require_once(LIMB_DIR . 'core/lib/cron/cron_manager.class.php');

$force = false;
if(isset($argv[2]) && $argv[2] == 'force')
	$force = true;

$response =& new nonbuffered_response();
$mgr =& new cron_manager();

$mgr->perform($response, $force);
  
$response->write(debug::parse_cli_console());

$response->commit();	

exit(0);
?>