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

function register_testing_ini($ini_file, $content)
{
  if (isset($GLOBALS['testing_ini'][$ini_file])) 
    die("Duplicate ini registration not allowed.");
  
  $instance_name = 'global_ini_instance_' . md5(VAR_DIR .  $ini_file);
    
  if(isset($GLOBALS[$instance_name]))
	 unset($GLOBALS[$instance_name]);    
  
  $GLOBALS['testing_ini'][$ini_file] = 1;
  
  $f = fopen(VAR_DIR . '/' . $ini_file, 'w');
    
  fwrite($f, $content, strlen($content));
  fclose($f);
  
}

function clear_testing_ini()
{
  if(!isset($GLOBALS['testing_ini']) || !count($GLOBALS['testing_ini']))
    return;
  
  foreach(array_keys($GLOBALS['testing_ini']) as $ini_file)
  {
    if(file_exists(VAR_DIR . '/' . $ini_file))
    {
      unlink(VAR_DIR . '/' . $ini_file);
    }
  }
  
  $GLOBALS['testing_ini'] = array();
  
  clearstatcache();  
  
	$instance_name = 'global_ini_instance_' . md5(VAR_DIR .  $ini_file);
  
  if(isset($GLOBALS[$instance_name]))
	 unset($GLOBALS[$instance_name]);
}

function load_testing_db_dump($dump_path)
{
	if(!file_exists($dump_path))
		die('"' . $dump_path . '" sql dump file not found!');
		
  $tables = array();
  $sql_array = file($dump_path);
  
  $db = db_factory::instance();
  	
	foreach($sql_array as $sql)
	{
		if(!preg_match("|insert\s+?into\s+?([^\s]+)|i", $sql, $matches))
		  continue;

		if(isset($tables[$matches[1]]))
		  continue;

		$tables[$matches[1]] = $matches[1];	
		$db->sql_delete($matches[1]);
	}
	
	$GLOBALS['testing_db_tables'] = $tables;
	
	foreach($sql_array as $sql)
	{ 
	  if(trim($sql))
		  $db->sql_exec($sql);
	}
}

function clear_testing_db_tables()
{
  if(!isset($GLOBALS['testing_db_tables']))
    return;
  
  $db = db_factory::instance();
  
	foreach($GLOBALS['testing_db_tables'] as $table)
		$db->sql_delete($table);
		
	$GLOBALS['testing_db_tables'] = array();
}

?>