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

require_once(LIMB_DIR . 'core/datasource/search_datasource.class.php');
require_once(LIMB_DIR . 'core/search_fetcher.class.php');

class search_sub_branch_datasource extends search_datasource
{
	function search_sub_branch_datasource()
	{
		parent :: search_datasource();		
	}

	function & _fetch(&$counter, $params)
	{
		if (!isset($params['path']))
			$params['path'] = '/root';

		if (!isset($params['loader_class_name']))
			$params['loader_class_name'] = 'site_object';

		if (!isset($params['fetch_method']))
			$params['fetch_method'] = 'fetch_by_ids';
			
		$arr =& search_fetch_sub_branch($params['path'], $params['loader_class_name'], $counter, $params, $params['fetch_method']);
		
		return $arr;
	}
}
?>