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

?>