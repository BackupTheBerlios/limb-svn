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
require_once('../setup.php');
require_once(LIMB_DIR . '/core/lib/db/db_table_factory.class.php');

function load_site_objects($dir_name, &$site_objects)
{ 
	if ($dir = opendir($dir_name))
	{  
		while(($object_file = readdir($dir)) !== false) 
		{  
			if  (substr($object_file, -10,  10) == '.class.php')
			{
				$class_name = substr($object_file, 0, strpos($object_file, '.'));
				
				if(!class_exists($class_name))
				{
					include_once($dir_name . '/' . $object_file);
					$site_objects[] = new $class_name();
				}
			} 
		} 
		closedir($dir); 
	} 
} 

$site_objects = array();

echo "loading site objects...\n";

load_site_objects(PROJECT_DIR . '/core/model/site_objects/', $site_objects);
load_site_objects(LIMB_DIR . '/core/model/site_objects/', $site_objects);

$type_db_table =& db_table_factory :: instance('sys_class');
foreach($site_objects as $object)
{
	$class_id = $object->get_class_id();
	
	$class_properties = $object->get_class_properties();
	
	echo "updating " . get_class($object)  . "...\n";
	
	$type_db_table->update_by_id($class_id, $class_properties);
}

echo 'done';

?>