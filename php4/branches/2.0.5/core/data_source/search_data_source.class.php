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

require_once(LIMB_DIR . 'core/data_source/fetch_data_source.class.php');
require_once(LIMB_DIR . 'core/search_fetcher.class.php');
require_once(LIMB_DIR . 'core/model/search/search_query.class.php');
require_once(LIMB_DIR . 'core/model/search/normalizers/search_text_normalizer.class.php');

class search_data_source extends fetch_data_source
{
	var $query_object = null;
	
	function search_data_source()
	{
		parent :: fetch_data_source();
		
		$this->query_object = new search_query();
		
		$this->_init_search_query_object();
		
		set_search_query_object($this->query_object);
	}

	function & _fetch(&$counter, $params)
	{
		if (!isset($params['loader_class_name']))
			$params['loader_class_name'] = 'site_object';

		if (!isset($params['fetch_method']))
			$params['fetch_method'] = 'fetch_accessible_by_ids';
				
		$arr =& search_fetch($params['loader_class_name'], $counter, $params, $params['fetch_method']);
		
		return $arr;
	}
	
	function _init_search_query_object()
	{
		if (isset($_REQUEST['search_query']) && trim($_REQUEST['search_query']))
		{
			$this->query_object->add(search_text_normalizer :: process($_REQUEST['search_query']));
		}
	}
}
?>