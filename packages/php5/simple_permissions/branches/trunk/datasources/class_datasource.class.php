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
require_once(LIMB_DIR . 'class/datasources/datasource.interface.php'); 

class class_datasource implements datasource
{
	public function get_dataset(&$counter, $params=array())
	{
		$counter = 0;

		if(!$class_id = LimbToolsBox :: getToolkit()->getRequest()->get('class_id'))
			return new array_dataset();
			
		$db_table = LimbToolsBox :: getToolkit()->createDBTable('sys_class');
		$class_data = $db_table->get_row_by_id($class_id);
		
		if ($class_data)
		{
			$counter = 1;
			return new array_dataset(array(0 => $class_data));
		}
		else
			return new array_dataset(array());
	}		
}


?>
