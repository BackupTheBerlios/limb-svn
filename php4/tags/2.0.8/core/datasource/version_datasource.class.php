<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: class_datasource.class.php 100 2004-03-30 12:21:26Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/datasource/datasource.class.php');

class version_datasource extends datasource
{
	function version_datasource()
	{
		parent :: datasource();
	}

	function & get_dataset(&$counter, $params=array())
	{
		$counter = 0;
		
		if(!isset($_REQUEST['version']))
			return new empty_dataset();

		if(!isset($_REQUEST['version_node_id']))
			return new empty_dataset();
			
		$version = (int)$_REQUEST['version'];
		$node_id = (int)$_REQUEST['version_node_id'];

		if(!$site_object = wrap_with_site_object(fetch_one_by_node_id($node_id)))
			return new empty_dataset();
		
		if(!is_subclass_of($site_object, 'content_object'))
			return new empty_dataset();
		
		if(($version_data = $site_object->fetch_version($version)) === false)
			return new empty_dataset();
				
		$result = array();
		
		foreach($version_data as $attrib => $value)
		{
			$data['attribute'] = $attrib;
			$data['value'] = $value;
			$result[] = $data;
		}
		
		return new array_dataset($result);
	}		
}


?>
