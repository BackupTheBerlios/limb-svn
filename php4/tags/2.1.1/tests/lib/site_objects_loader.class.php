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

class site_objects_loader
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
	  return array();
	}
}

?>