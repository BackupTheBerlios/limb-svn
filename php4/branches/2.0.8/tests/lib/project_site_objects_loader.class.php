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
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/core/model/site_object_factory.class.php');

class project_site_objects_loader
{
	function & get_site_objects()
	{
		$site_objects = array();
		foreach($this->get_classes_list() as $class)
		{
			$site_objects[] =& site_object_factory :: create($class);
		}
		
		return $site_objects;
	}
	
	function get_classes_list()
	{
		$project_db = str_replace('_tests', '', DB_NAME);
		
		$db =& db_factory :: instance();
		
		$db->select_db($project_db);
		
		$db->sql_select('sys_class', '*', 'class_name != "site_object"');
		
		$list = $db->get_array();
		
		$db->select_db(DB_NAME);
		
		return complex_array :: get_column_values('class_name', $list);
	}
}

?>