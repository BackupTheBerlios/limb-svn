<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
$site_path = $argv[1];
require_once($site_path . '/setup.php');

require_once(LIMB_DIR . 'class/lib/error/debug.class.php');
require_once(LIMB_DIR . 'class/core/request/nonbuffered_response.class.php');
require_once(LIMB_DIR . 'class/lib/cron/cron_manager.class.php');

$force = false;
if(isset($argv[2]) && $argv[2] == 'force')
	$force = true;

$response = new nonbuffered_response();
$mgr = new cron_manager();

$mgr->perform($response, $force);
  
$response->write(debug::parse_cli_console());

$response->commit();	

exit(0);
?>