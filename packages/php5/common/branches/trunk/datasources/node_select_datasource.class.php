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
require_once(LIMB_DIR . 'class/datasources/fetch_sub_branch_datasource.class.php');

class node_select_datasource extends fetch_sub_branch_datasource
{
	function get_dataset(&$counter, $params = array())
	{
		$params['depth'] = 1;

	  $request = request :: instance();		

		if($request->get('only_parents') == 'false')
			$params['only_parents'] = false;
		else
			$params['only_parents'] = true;

		$params['restrict_by_class'] = false;
		$params['path'] = $this->_process_path();

		return parent :: get_dataset($counter, $params);
	}
	
	function _process_path()
	{
		$default_path = '/root/';

	  $request = request :: instance();		
		
		if(!$path = $request->get('path'))
			return $default_path;

		if(strpos($path, '?') !== false)
		{
			if(!$node = map_url_to_node($path))
				return $default_path;
				
			if(!$path = tree :: instance()->get_path_to_node($node))
				return $default_path;
		}
		return $path;
	}

}



?>