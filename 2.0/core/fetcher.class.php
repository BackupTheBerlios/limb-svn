<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: fetcher.class.php 463 2004-02-18 08:21:31Z server $
*
***********************************************************************************/ 

require_once(LIMB_DIR . 'core/tree/limb_tree.class.php');
require_once(LIMB_DIR . 'core/model/site_object_factory.class.php');
require_once(LIMB_DIR . 'core/lib/http/url_parser.class.php');
require_once(LIMB_DIR . 'core/model/access_policy.class.php');

class fetcher
{
	var $_custom_class_loaders = array();
	var $_node_mapped_by_url = null;

	function fetcher()
	{
	}
	
	function &instance()
	{
		$obj =&	instantiate_object('fetcher');
		return $obj;
	}
	
	function & fetch($loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch')
	{
		$counter = 0;
		$count_method = $fetch_method . '_count';
		
		$site_object =& site_object_factory :: instance($loader_class_name);
		$counter = $site_object->$count_method($params);
		
		$result =& $site_object->$fetch_method($params);
		
		if(!count($result))
			return array();
		
		$access_policy = access_policy :: instance();
		$access_policy->assign_actions_to_objects($result);

		$this->_assign_paths($result);
		return $result;
	}
		
	function & fetch_sub_branch($path, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
	{
		$tree =& limb_tree :: instance();
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
		
		$result =& $this->fetch_by_ids($object_ids, $loader_class_name, $counter, $params, $fetch_method);
		
		return $result;
	}
	
	function & fetch_by_ids($object_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
	{		
		$counter = 0;
		$count_method = $fetch_method . '_count';
		
		$site_object =& site_object_factory :: instance($loader_class_name);
		
		$counter = $site_object->$count_method($object_ids, $params);
		
		$result =& $site_object->$fetch_method($object_ids, $params);
		
		if(!count($result))
			return array();
		
		$access_policy = access_policy :: instance();
		$access_policy->assign_actions_to_objects($result);

		$this->_assign_paths($result);
		
		if (isset($params['order']))//assumed it's already ordered by site_object
			return $result;
				
		$ids_sorted_result = array();
		foreach($object_ids as $id)
		{
			if(isset($result[$id]))
				$ids_sorted_result[$id] =& $result[$id];
		}
		
		return $ids_sorted_result;
	}
	
	function & fetch_by_node_ids($node_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
	{
		$object_ids = array();
		$tree =& limb_tree :: instance();

		foreach($node_ids as $key)
			if (!isset($object_ids[$key]))
			{
				if($node = $tree->get_node($key))
					$object_ids[$key] = $node['object_id'];
			}	
		
		$objects =& $this->fetch_by_ids($object_ids, $loader_class_name, $counter, $params, $fetch_method);
		$result = array();
		
		foreach($object_ids as $node_id => $object_id)
		{
			if (isset($objects[$object_id]))
				$result[$node_id] =& $objects[$object_id];
		}
		
		return $result;
	}
				
	function & fetch_one_by_node_id($node_id)
	{
		$tree =& limb_tree :: instance();

		if (!$node = $tree->get_node($node_id))
			return false;

		$access_policy = access_policy :: instance();
		$object_ids = $access_policy->get_accessible_objects(array($node['object_id']));
		
		if (!count($object_ids))
			return false;
		
		$object_id = reset($object_ids);
		if ($class_name = $this->_get_object_class_name_by_id($object_id))
		{
			$site_object =& site_object_factory :: instance($class_name);
			$result =& $site_object->fetch_by_ids(array($object_id));
			
			if (!count($result))
				return false;

			$access_policy->assign_actions_to_objects($result);
	
			$this->_assign_paths($result);
	
			return reset($result);
		}
		else
			return false;	
	}
	
	function _get_object_class_name_by_id($object_id)
	{
		$db =& db_factory :: instance();
		
		$sql = "SELECT sc.class_name 
			FROM sys_site_object as sso, sys_class as sc
			WHERE sso.class_id = sc.id
			AND sso.id={$object_id}";
		
		$db->sql_exec($sql);
		$row = $db->fetch_row();
		if (!isset($row['class_name']))
		{
			debug :: write_error('object class name not found',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array(
    			'object_id' => $object_id
    		)
    	);
			return false;
		}
		else
			return $row['class_name'];
	}
	
	function & fetch_one_by_path($path)
	{
		$tree =& limb_tree :: instance();
		
		$node = $tree->get_node_by_path($path);
		
		if (!$node)
			return false;
		
		$result =& $this->fetch_one_by_node_id($node['id']);
		return $result;	
	}
	
	function & fetch_mapped_by_url()
	{
		$node =& $this->map_url_to_node();
		$object_data =& $this->fetch_one_by_node_id($node['id']);
		
		return $object_data;
	}

	function & map_url_to_node($url = '', $recursive = false)
	{
		if($this->_node_mapped_by_url)
			return $this->_node_mapped_by_url;
		
		$tree =& limb_tree :: instance();
		
		if($url == '')
		{
			if(isset($_REQUEST['node_id']))
			{
				$node =& $tree->get_node((int)$_REQUEST['node_id']);
				
				$this->_node_mapped_by_url =& $node;
				
				return $node;
			}
			else
				$url = $_SERVER['PHP_SELF'];
		}
		
		$url_parser = new url_parser();
		$url_parser->parse($url);
		
		$node =& $tree->get_node_by_path($url_parser->path);
		
		$this->_node_mapped_by_url =& $node;
		
		return $node;
	}

	function _assign_paths(&$objects_array, $append = '')
	{
		$tree =& limb_tree :: instance();
		
		$parent_paths = array();
		
		foreach($objects_array as $key => $data)
		{
			$parent_id = $data['parent_id'];
			if (!isset($parent_paths[$parent_id]))
			{
				$parents = $tree->get_parents($data['node_id']);
				$path = '';
				foreach($parents as $parent_data)
					$path .= '/' . $parent_data['identifier'];
				
				$parent_paths[$parent_id] = $path;	
			}

			$objects_array[$key]['path'] = $parent_paths[$parent_id] . '/' . $data['identifier'] . $append;
		}
	}
}

function & map_url_to_node($url = '', $recursive = false)
{
	$fetcher =& fetcher :: instance();
	$result =& $fetcher->map_url_to_node($url, $recursive);
	return $result;
}

function & fetch_mapped_by_url()
{
	$fetcher =& fetcher :: instance();
	$result =& $fetcher->fetch_mapped_by_url();
	return $result;
}

function & fetch_one_by_node_id($node_id)
{
	$fetcher =& fetcher :: instance();
	$result =& $fetcher->fetch_one_by_node_id($node_id);
	return $result;
}

function & fetch_one_by_path($path)
{
	$fetcher =& fetcher :: instance();
	$result =& $fetcher->fetch_one_by_path($path);
	return $result;
}

function & fetch($loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch')
{
	$fetcher =& fetcher :: instance();
	$result =& $fetcher->fetch($loader_class_name, $counter, $params, $fetch_method);
	return $result;
}

function & fetch_sub_branch($path, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
{
	$fetcher =& fetcher :: instance();
	$result =& $fetcher->fetch_sub_branch($path, $loader_class_name, $counter, $params, $fetch_method);
	return $result;
}

function & fetch_by_ids($object_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
{
	$fetcher =& fetcher :: instance();
	$result =& $fetcher->fetch_by_ids($object_ids, $loader_class_name, $counter, $params, $fetch_method);
	return $result;
}


function & get_mapped_controller()
{
	$object_data =& fetch_mapped_by_url();
	
	$site_object =& site_object_factory :: instance($object_data['class_name']);
	
	$controller =& $site_object->get_controller();
	
	return $controller;
}

?>