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

ob_start();

$site_path = $argv[1];

if (file_exists($site_path . '/setup_custom.php'))
	include_once($site_path . '/setup_custom.php');

require_once($site_path . '/setup.php');
require_once(LIMB_DIR . 'core/lib/util/ini.class.php');
require_once(LIMB_DIR . 'core/lib/debug/debug.class.php');

$cron_scripts_dir = $site_path . '/cron/';
$ini =& ini::instance('cron.ini', $cron_scripts_dir, false);

$cron_last_run_file = $cron_scripts_dir . '.scripts_last_run';

$scripts_last_run = array();

if(file_exists($cron_last_run_file))
{
	$fp = fopen($cron_last_run_file, 'r');
	
	if($contents = fread ($fp, filesize($cron_last_run_file)))
		$scripts_last_run = explode("\n", $contents);
		
	fclose($fp);
}

$scripts =& $ini->variable('cron', 'scripts');

foreach($scripts as $id => $script_string)
{
	if(!isset($scripts_last_run[$id]))
		$scripts_last_run[$id] = 0;

	$script_settings = explode(';', $script_string);
	
	if(sizeof($script_settings) > 1)
		$script_run_interval = (int)$script_settings[1];
	else
		$script_run_interval = 3600;
	
	$script_name = $script_settings[0];
	
	$script_file = $cron_scripts_dir . $script_name;
	
	$now = time();

  if(file_exists($script_file))
  { 
  	$time_diff = $now - (int)$scripts_last_run[$id];
  	
  	if($time_diff > $script_run_interval)
  	{
    	echo "running $script_name\n";
    	
    	debug::add_timing_point( "script $script_name starting\n" );
    	
  		include( $script_file );
  		
  		debug::add_timing_point( "script done\n" );
			
			$scripts_last_run[$id] = $now;    		
  	}
  	else
  		echo "will run $script_name in " . ($script_run_interval - $time_diff) . " seconds\n";
  }
  else
  	echo "$script_file not found\n";
}

$fp = fopen($cron_last_run_file, 'w');

foreach($scripts_last_run as $time)
	fwrite($fp, "$time\n");
	
fclose($fp);
	
//echo debug::parse_report(false, false);

ob_end_flush();
?>