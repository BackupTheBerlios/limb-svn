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

if(isset($argv[1]))
  $project_dir = $argv[1];
else
  die('project dir required');

require_once($project_dir . '/setup.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');

$db = db_factory::instance();

$db->sql_delete('sys_stat_log');
$db->sql_delete('sys_stat_counter');
$db->sql_delete('sys_stat_day_counters');
$db->sql_delete('sys_stat_ip');
$db->sql_delete('sys_stat_uri');
$db->sql_delete('sys_stat_referer_url');
$db->sql_delete('sys_stat_search_phrase');

?>