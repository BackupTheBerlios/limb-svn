<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: fetch_sub_branch_datasource.class.php 100 2004-03-30 12:21:26Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/fetcher.class.php');
require_once(LIMB_DIR . 'core/datasource/fetch_sub_branch_datasource.class.php');

class parents_only_datasource extends fetch_sub_branch_datasource
{
	function parents_only_datasource()
	{
		parent :: fetch_sub_branch_datasource();
	}
	
	function & _fetch(&$counter, $params)
	{
		$params['depth'] = 1;
		$params['only_parents'] = true;
		$params['restrict_by_class'] = false;

		if(isset($_REQUEST['path']))
		{
			if(!$path = $this->_process_path($_REQUEST['path']))
				$path = '/root/';
				
			$params['path'] = $path;
		}
		else
			$params['path'] = '/root/';

		return parent :: _fetch($counter, $params);
	}
	
	function _process_path($path)
	{
		if(strpos($path, '?') !== false)
		{
			if(!$node = map_url_to_node($path))
				return false;
				
			$tree =& limb_tree :: instance();
			if(!$path = $tree->get_path_to_node($node))
				return false;
		}
		return $path;
	}

}



?>