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

require_once(LIMB_DIR . 'core/fetcher.class.php');
require_once(LIMB_DIR . 'core/model/search/full_text_search.class.php');

class search_fetcher extends fetcher
{
	var $_query_object = null;
	
	function search_fetcher()
	{
		parent :: fetcher();
	}
	
	function &instance()
	{
		$obj =&	instantiate_object('search_fetcher');
		return $obj;
	}
	
	function set_search_query_object($query_object)
	{
		$this->_query_object = $query_object;
	}
	
	function & search_fetch($loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_accessible_by_ids')
	{
		if (!$this->_query_object)
		{
			 debug :: write_error('search_query is empty',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array()
    	);
    	return array();
    }	

		$site_object =& site_object_factory :: instance($loader_class_name);

		if (!isset($params['restrict_by_class']) ||
				(isset($params['restrict_by_class']) && (bool)$params['restrict_by_class']))
			$class_id = $site_object->get_class_id();
		else
			$class_id = null;

		$search =& new full_text_search();

		$search_result = $search->find($this->_query_object, $class_id);
		if (!count($search_result))
			return array();
		
		$counter = 0;
		$count_method = $fetch_method . '_count';

		$counter = $site_object->$count_method(array_keys($search_result), $params);
		$fetched_objects =& $site_object->$fetch_method(array_keys($search_result), $params);

		if(!count($fetched_objects))
			return array();
		
		foreach($search_result as $key => $score)
		{
			if (isset($fetched_objects[$key]))
			{
				$result[$key] = $fetched_objects[$key];
				$result[$key]['score'] = $score;
			}	
		}
		
		$access_policy = access_policy :: instance();
		$access_policy->assign_actions_to_objects($result);

		$this->_assign_paths($result);
		$this->_assign_search_paths($result, isset($params['offset']) ? $params['offset'] : 0);
		
		return $result;
	}
	
	function & search_fetch_sub_branch($path, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch')
	{
		$tree =& tree :: instance();
		$site_object =& site_object_factory :: instance($loader_class_name);
		
		if (!isset($params['restrict_by_class']) ||
				(isset($params['restrict_by_class']) && (bool)$params['restrict_by_class']))
			$class_id = $site_object->get_class_id();
		else
			$class_id = null;
		
		if (isset($params['check_expanded_parents']))
			$check_expanded_parents = (bool)$params['check_expanded_parents'];
		else
			$check_expanded_parents = false;
			
		if (isset($params['include_parent']))
			$include_parent = (bool)$params['include_parent'];
		else
			$include_parent = false;
		
		$depth = isset($params['depth']) ? $params['depth'] : 1;	
		
		if(!$nodes = $tree->get_accessible_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents, $class_id))
			return array();
						
		$object_ids = complex_array :: get_column_values('object_id', $nodes);
		
		if (!count($object_ids))
			return array();
		
		$result =& $this->search_fetch_by_ids($object_ids, $loader_class_name, $counter, $params, $fetch_method);
		
		return $result;
	}

	function & search_fetch_by_ids($object_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
	{
		if (!$this->_query_object)
		{
			 debug :: write_error('search_query is empty',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array()
    	);
    	return array();
    }	

		$search =& new full_text_search();
		$search_result = $search->find_by_ids($object_ids, $this->_query_object);

		if(!count($search_result))
			return array();
		
		$counter = 0;
		$count_method = $fetch_method . '_count';
		
		$site_object =& site_object_factory :: instance($loader_class_name);
		$counter = $site_object->$count_method(array_keys($search_result), $params);
		$fetched_objects =& $site_object->$fetch_method(array_keys($search_result),$params);

		if(!count($fetched_objects))
			return array();
		
		foreach($search_result as $key => $score)
			if (isset($fetched_objects[$key]))
			{
				$result[$key] = $fetched_objects[$key];
				$result[$key]['score'] = $score;
			}	
		
		$access_policy = access_policy :: instance();
		$access_policy->assign_actions_to_objects($result);

		$this->_assign_paths($result);
		$this->_assign_search_paths($result, isset($params['offset']) ? $params['offset'] : 0);
		
		return $result;
	}
	
	function _assign_search_paths(& $objects_array, $offset = 0)
	{
		$query = $this->_query_object->to_string();
		
		$counter = 0;
		foreach($objects_array as $key => $data)
		{
			$counter++;
			
			if(!isset($objects_array[$key]['title']) || !$objects_array[$key]['title'])
				$objects_array[$key]['title'] = $objects_array[$key]['path'];
			
			$objects_array[$key]['search_path'] = $objects_array[$key]['path'] . '?h=' . urlencode($query);
			$objects_array[$key]['search_full_path'] = 'http://' . $_SERVER['HTTP_HOST'] . $objects_array[$key]['path'];
			$objects_array[$key]['search_counter'] = $offset + $counter;
		}
	}
}

function set_search_query_object($query_object)
{
	$search_fetcher =& search_fetcher :: instance();
	$search_fetcher->set_search_query_object($query_object);
}

function & search_fetch($loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_accessible_by_ids')
{
	$search_fetcher =& search_fetcher :: instance();
	$result =& $search_fetcher->search_fetch($loader_class_name, $counter, $params, $fetch_method);
	return $result;
}

function & search_fetch_sub_branch($path, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_accessible_by_ids')
{
	$search_fetcher =& search_fetcher :: instance();
	$result =& $search_fetcher->search_fetch_sub_branch($path, $loader_class_name, $counter, $params, $fetch_method);
	return $result;
}
?>