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
require_once(LIMB_DIR . 'class/lib/system/objects_support.inc.php');

if(!is_registered_resolver('db_table'))
  register_file_resolver('db_table', LIMB_DIR . '/class/core/file_resolvers/db_table_file_resolver');

class db_table_factory
{
  protected function __construct(){}
  		
	static function create($db_table_name)
	{	
	  self :: _include_class_file($db_table_name);
	  
	  $klass = $db_table_name . '_db_table';
	  
	  return new $klass();
	}
	
	static protected function _include_class_file($db_table_name)
	{
	  if(class_exists($db_table_name . '_db_table'))
	    return;
	  
		resolve_handle($resolver =& get_file_resolver('db_table'));
		
		$full_path = $resolver->resolve($db_table_name);

		include_once($full_path);
	}	
}
?>