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
$site_path = $argv[1];
require_once($site_path . '/setup.php');

require_once(LIMB_DIR . '/core/error/Debug.class.php');
require_once(LIMB_DIR . '/core/request/NonbufferedResponse.class.php');
require_once(LIMB_DIR . '/core/cron/CronManager.class.php');

$force = false;
if(isset($argv[2]) &&  $argv[2] == 'force')
  $force = true;

$response = new NonbufferedResponse();
$mgr = new CronManager();

$mgr->perform($response, $force);

$response->write(Debug::parseCliConsole());

$response->commit();

exit(0);
?>