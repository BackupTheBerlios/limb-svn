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
require_once(LIMB_DIR . 'class/core/tree/tree.class.php');
require_once(LIMB_DIR . 'class/core/site_objects/site_object_factory.class.php');
require_once(LIMB_DIR . 'class/lib/http/uri.class.php');
require_once(LIMB_DIR . 'class/core/request/request.class.php');
require_once(LIMB_DIR . 'class/core/access_policy.class.php');

class fetcher
{
  protected static $_instance = null;
  
	protected $_custom_class_loaders = array();
	
	protected $_node_mapped_by_request = null;
	
	protected $_cached_objects = array('path' => array(), 'node_id' => array(), 'id' => array());
	
	protected $_is_jip_enabled = false;
	
	static public function instance()
	{
    if (!self :: $_instance)
      self :: $_instance = new fetcher();

    return self :: $_instance;	
	}
	
	protected function _get_access_policy()
	{
	  include_once(LIMB_DIR . 'class/core/access_policy.class.php');
	  $access_policy = access_policy :: instance();
	  return $access_policy;
	}
	
	public function is_jip_enabled()
	{
	  return $this->_is_jip_enabled;
	}
	
	public function set_jip_status($status=true)
	{
	  $prev = $this->_is_jip_enabled;
	  $this->_is_jip_enabled = $status;
	  return $prev;
	}
	
	public function flush_cache()
	{
	  $this->_cached_objects = array('path' => array(), 'node_id' => array(), 'id' => array());
	}
	
	protected function _place_object_to_cache($object_data, $cache_type='auto', $cache_id='')
	{
	  if($cache_type == 'auto')
	  {
	    $this->_cached_objects['path'][$object_data['path']] = $object_data;
	    $this->_cached_objects['path'][$object_data['path'] . '/'] = $object_data;
	    $this->_cached_objects['node_id'][$object_data['node_id']] = $object_data;
	    $this->_cached_objects['id'][$object_data['id']] = $object_data;
	  }
	  else
	    $this->_cached_objects[$cache_type][$cache_id] = $object_data;
	}
	
	protected function _get_object_from_cache($cache_type, $cache_id)
	{
	  if(isset($this->_cached_objects[$cache_type][$cache_id]))
	    return $this->_cached_objects[$cache_type][$cache_id];
	}
	
	protected function _assign_actions(&$objects_data)
	{
    if ($this->is_jip_enabled())
    {
		  $access_policy = $this->_get_access_policy();
		  $access_policy->assign_actions_to_objects($objects_data);
		}
	}
	
	public function fetch($loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch')
	{
		$counter = 0;
		$count_method = $fetch_method . '_count';
		
		$site_object = site_object_factory :: create($loader_class_name);
		$counter = $site_object->$count_method($params);
		
		$result = $site_object->$fetch_method($params);
		
		if(!count($result))
			return array();

    $this->_assign_actions($result);

		$this->_assign_paths($result);
		return $result;
	}
		
	public function fetch_sub_branch($path, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
	{		
		$tree = tree :: instance();
		$site_object = site_object_factory :: create($loader_class_name);
		
		if ($loader_class_name != 'site_object' &&
				!isset($params['restrict_by_class']) ||
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

		if (isset($params['only_parents']))
			$only_parents = (bool)$params['only_parents'];
		else
			$only_parents = false;
		
		$depth = isset($params['depth']) ? $params['depth'] : 1;	
		
		if(!$nodes = $tree->get_accessible_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents, $class_id, $only_parents))
			return array();
						
		if(!$object_ids = complex_array :: get_column_values('object_id', $nodes))
			return array();
				
		$objects_data = $this->fetch_by_ids($object_ids, $loader_class_name, $counter, $params, $fetch_method);
		
		return $objects_data;
	}
	
	public function fetch_by_ids($object_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
	{		
		$counter = 0;
		$count_method = $fetch_method . '_count';
		
		$site_object = site_object_factory :: create($loader_class_name);
		
		$counter = $site_object->$count_method($object_ids, $params);
		
		$result = $site_object->$fetch_method($object_ids, $params);
		
		if(!is_array($result) || !count($result))
			return array();

    $this->_assign_actions($result);

		$this->_assign_paths($result);
		
		if (isset($params['order']))//assumed it's already ordered by site_object
			return $result;
				
		$ids_sorted_result = array();
		foreach($object_ids as $id)
		{
			if(isset($result[$id]))
				$ids_sorted_result[$id] = $result[$id];
		}
		
		return $ids_sorted_result;
	}
	
	public function  fetch_by_node_ids($node_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
	{
		$object_ids = array();
		$tree = tree :: instance();
    
    $nodes = $tree->get_nodes_by_ids($node_ids);
    if (!is_array($nodes) || !count($nodes))
      return array();
    
		foreach($nodes as $node)
			$object_ids[$node['id']] = $node['object_id'];
		
		$objects_data = $this->fetch_by_ids($object_ids, $loader_class_name, $counter, $params, $fetch_method);
		$sorted_objects_data = array();
		
		foreach($node_ids as $node_id)
		{
		  if (!isset($object_ids[$node_id]))
		    continue;
		    
			if (isset($objects_data[$object_ids[$node_id]]))
				$sorted_objects_data[$node_id] = $objects_data[$object_ids[$node_id]];
		}
		
		return $sorted_objects_data;
	}
	
	public function fetch_one_by_id($object_id)
	{
	  if($object_data = $this->_get_object_from_cache('id', $object_id))
	    return $object_data;
	  
	  $access_policy = $this->_get_access_policy();
		$object_ids = $access_policy->get_accessible_objects(array($object_id));
		
		if (!is_array($object_ids) || !count($object_ids))
			return false;
		
		$object_id = reset($object_ids);

		$site_object = site_object_factory :: create($this->_get_object_class_name_by_id($object_id));
		
		$result = $site_object->fetch_by_ids(array($object_id));
					
		if (!is_array($result) || !count($result))
			return false;

    $this->_assign_actions($result);

		$this->_assign_paths($result);
		
		$object_data = reset($result);
		
		$this->_place_object_to_cache($object_data);

		return $object_data;
	}
	
	public function fetch_one_by_node_id($node_id)
	{
	  if($object_data = $this->_get_object_from_cache('node_id', $node_id))
	    return $object_data;
	
		$tree = tree :: instance();

		if (!$node = $tree->get_node($node_id))
			return false;
			
		$object_data = $this->fetch_one_by_id($node['object_id']);
		
		return $object_data;
	}
		
	protected function _get_object_class_name_by_id($object_id)
	{
		$db = db_factory :: instance();
		
		$sql = "SELECT sc.class_name 
			FROM sys_site_object as sso, sys_class as sc
			WHERE sso.class_id = sc.id
			AND sso.id={$object_id}";
		
		$db->sql_exec($sql);
		$row = $db->fetch_row();
		if (!isset($row['class_name']))
		{
			throw new LimbException('object class name not found',
    		array(
    			'object_id' => $object_id
    		)
    	);
		}
		else
			return $row['class_name'];
	}
	
	public function fetch_one_by_path($path)
	{
	  if($object_data = $this->_get_object_from_cache('path', $path))
	    return $object_data;
	
		$tree = tree :: instance();
		
		if (!$node = $tree->get_node_by_path($path))
			return false;
		
		$object_data = $this->fetch_one_by_node_id($node['id']);
		return $object_data;	
	}
	
	public function fetch_requested_object($request = null)
	{
	  if($request === null)
	    $request = request :: instance();
	  
		if(!$node = $this->map_request_to_node())
			return array();
		
		$prev_jip_status	= $this->set_jip_status(true);
		
		$object_data = $this->fetch_one_by_node_id($node['id']);
		
		$this->set_jip_status($prev_jip_status);
		
		return $object_data;
	}

	public function map_url_to_node($url, $recursive = false)
	{	
		$tree = tree :: instance();
				
		$uri = new uri($url);
		
		if(($node_id = $uri->get_query_item('node_id')) === false)
			$node = $tree->get_node_by_path($uri->get_path(), '/', $recursive);
		else
			$node = $tree->get_node((int)$node_id);
		
		return $node;
	}
	
	public function map_request_to_node($request = null)
	{
		if($this->_node_mapped_by_request)
			return $this->_node_mapped_by_request;
		
	  if($request === null)
	    $request = request :: instance();			
			
		if($node_id = $request->get('node_id'))
		{
			$tree = tree :: instance();
			
			$node = $tree->get_node((int)$node_id);
			
			$this->_node_mapped_by_request = $node;
			
			return $node;
		}
		else
			$url = $_SERVER['PHP_SELF'];
		
		$node = $this->map_url_to_node($url);
					
		$this->_node_mapped_by_request = $node;
		
		return $node;
	}

	protected function _assign_paths(&$objects_array, $append = '')
	{
		$tree = tree :: instance();
				
		$parent_paths = array();
		
		foreach($objects_array as $key => $data)
		{
			$parent_node_id = $data['parent_node_id'];
			if (!isset($parent_paths[$parent_node_id]))
			{
				$parents = $tree->get_parents($data['node_id']);
				$path = '';
				foreach($parents as $parent_data)
					$path .= '/' . $parent_data['identifier'];
				
				$parent_paths[$parent_node_id] = $path;	
			}

			$objects_array[$key]['path'] = $parent_paths[$parent_node_id] . '/' . $data['identifier'] . $append;
		}
	}
}

function map_request_to_node($request = null)
{
	return fetcher :: instance()->map_request_to_node($request);
}

function flush_cache()
{
	return fetcher :: instance()->flush_cache();
}

function map_url_to_node($url, $recursive = false)
{
	return fetcher :: instance()->map_url_to_node($url, $recursive);
}

function fetch_requested_object()
{
	return fetcher :: instance()->fetch_requested_object();
}

function fetch_one_by_id($object_id)
{
  return fetcher :: instance()->fetch_one_by_id($object_id);
}

function fetch_one_by_node_id($node_id)
{
	return fetcher :: instance()->fetch_one_by_node_id($node_id);
}

function fetch_one_by_path($path)
{
  return fetcher :: instance()->fetch_one_by_path($path);
}

function fetch($loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch')
{
  return fetcher :: instance()->fetch($loader_class_name, $counter, $params, $fetch_method);
}

function fetch_sub_branch($path, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
{
  return fetcher :: instance()->fetch_sub_branch($path, $loader_class_name, $counter, $params, $fetch_method);
}

function fetch_by_ids($object_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
{
  return fetcher :: instance()->fetch_by_ids($object_ids, $loader_class_name, $counter, $params, $fetch_method);
}

function fetch_by_node_ids($node_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
{
  return fetcher :: instance()->fetch_by_node_ids($node_ids, $loader_class_name, $counter, $params, $fetch_method);
}

function wrap_with_site_object($fetched_data)
{
	if(!$fetched_data)
		return false;
		
	if(!is_array($fetched_data))
		return false;
		
	if(isset($fetched_data['class_name']))
	{
		$site_object = site_object_factory :: create($fetched_data['class_name']);
		$site_object->merge($fetched_data);
		return $site_object;
	}
	
	$site_objects = array();
	foreach($fetched_data as $id => $data)
	{
		$site_object = site_object_factory :: create($data['class_name']);
		$site_object->merge($data);
		$site_objects[$id] = $site_object;
	}
	return $site_objects;	
}
?>