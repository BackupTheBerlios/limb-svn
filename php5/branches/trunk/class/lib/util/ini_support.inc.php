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
require_once(LIMB_DIR . '/class/lib/util/ini.class.php');
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolvers_registry.inc.php');

if(!is_registered_resolver('ini'))
  register_file_resolver('ini', LIMB_DIR . '/class/core/file_resolvers/ini_file_resolver');

function get_ini_option($file_path, $var_name, $group_name = 'default', $use_cache = null)
{
	return get_ini($file_path, $use_cache)->get_option($var_name, $group_name);
} 

function & get_ini($file_name, $use_cache = null)
{
  if (isset($GLOBALS['testing_ini'][$file_name]))
  {
  	$resolved_file = VAR_DIR . $file_name;
    $use_cache = false;
  }
  else
  {
    resolve_handle($resolver =& get_file_resolver('ini'));
    $resolved_file = $resolver->resolve($file_name);  
  }  
  
	if (!($ini =& ini::instance($resolved_file, $use_cache)))
		error('couldnt retrieve ini instance', __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__, 
		array('file' => $resolved_file));

	return $ini;
} 

?>