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
require_once(LIMB_DIR . 'class/lib/http/uri.class.php');
require_once(LIMB_DIR . 'class/core/request/request.class.php');

class fetcher
{
  protected static $_instance = null;

	protected $_custom_class_loaders = array();

	protected $_node_mapped_by_request = null;

	protected $_cached_objects = array('path' => array(), 'node_id' => array(), 'id' => array());

	protected $_is_jip_enabled = true;

	static public function instance()
	{
    if (!self :: $_instance)
      self :: $_instance = new fetcher();

    return self :: $_instance;
	}

  //for mocking
	protected function _get_authorizer()
	{
	  include_once(LIMB_DIR . 'class/core/permissions/authorizer.class.php');
	  return authorizer :: instance();
	}

  //for mocking
	protected function _get_tree()
	{
	  include_once(LIMB_DIR . 'class/core/tree/tree.class.php');
	  return tree :: instance();
	}

  //for mocking
  protected function _get_site_object($class_name)
  {
    include_once(LIMB_DIR . 'class/core/site_objects/site_object_factory.class.php');
    return site_object_factory :: create($class_name);
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
	  $this->_node_mapped_by_request = null;
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
		  $this->_get_authorizer()->assign_actions_to_objects($objects_data);
		}
	}

	public function fetch_sub_branch($path, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
	{
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

		if(!$nodes = $this->_get_tree()->get_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents, $only_parents))
			return array();

		if(!$object_ids = complex_array :: get_column_values('object_id', $nodes))
			return array();

		return $this->fetch_by_ids($object_ids, $loader_class_name, $counter, $params, $fetch_method);
	}

	public function fetch_by_ids($object_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
	{
		$site_object = $this->_get_site_object($loader_class_name);

		if ($loader_class_name != 'site_object' &&
				!isset($params['restrict_by_class']) ||
				(isset($params['restrict_by_class']) && (bool)$params['restrict_by_class']))
		{
			$class_id = $site_object->get_class_id();
		}
		else
			$class_id = null;

		$object_ids = $this->_get_authorizer()->get_accessible_object_ids($object_ids, '', $class_id);

		if (!count($object_ids))
		  return array();

    if(!is_null($counter))
    {
  		$counter = 0;
  		$count_method = $fetch_method . '_count';
		  $counter = $site_object->$count_method($object_ids, $params);
		}

		$result = $site_object->$fetch_method($object_ids, $params);

		if(!is_array($result) || !count($result))
			return array();

    $this->_assign_actions($result);

		$this->_assign_paths($result);

		return $result;
	}

	public function fetch_by_node_ids($node_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
	{
    $nodes = $this->_get_tree()->get_nodes_by_ids($node_ids);
    if (!is_array($nodes) || !count($nodes))
      return array();

		$object_ids = complex_array :: get_column_values('object_id', $nodes);

		$objects_data = $this->fetch_by_ids($object_ids, $loader_class_name, $counter, $params, $fetch_method);

		if(isset($params['use_node_ids_as_keys']))
		{
  		$result = array();

  		foreach($objects_data as $object_data)
  			$result[$object_data['node_id']] = $object_data;

		  return $result;
	  }
	  else
	    return $objects_data;
	}

	public function fetch_one_by_id($object_id)
	{
	  if($object_data = $this->_get_object_from_cache('id', $object_id))
	    return $object_data;

    $loader_class_name = $this->_get_object_class_name_by_id($object_id);
    $objects_data = $this->fetch_by_ids(array($object_id), $loader_class_name, $counter = 0);
		$result = reset($objects_data);

		$this->_place_object_to_cache($result);

		return $result;
	}

	public function fetch_one_by_node_id($node_id)
	{
	  if($object_data = $this->_get_object_from_cache('node_id', $node_id))
	    return $object_data;

		if (!$node = $this->_get_tree()->get_node($node_id))
			return false;

		$object_data = $this->fetch_one_by_id($node['object_id']);

		return $object_data;
	}

  //for mocking
	protected function _get_object_class_name_by_id($object_id)
	{
	  return site_object :: _get_object_class_name_by_id($object_id);
	}

	public function fetch_one_by_path($path)
	{
	  if($object_data = $this->_get_object_from_cache('path', $path))
	    return $object_data;

		if (!$node = $this->_get_tree()->get_node_by_path($path))
			return false;

		return $this->fetch_one_by_id($node['object_id']);
	}

	public function fetch_requested_object($request = null)
	{
	  if($request === null)
	    $request = request :: instance();

		if(!$node = $this->map_request_to_node($request))
			return array();

		$prev_jip_status	= $this->set_jip_status(true);

		$object_data = $this->fetch_one_by_id($node['object_id']);

		$this->set_jip_status($prev_jip_status);

		return $object_data;
	}

	public function map_uri_to_node($uri, $recursive = false)
	{
		$tree = $this->_get_tree();

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
			$node = $this->_get_tree()->get_node((int)$node_id);
		else
		  $node = $this->map_uri_to_node($request->get_uri());

		$this->_node_mapped_by_request = $node;
		return $node;
	}

	protected function _assign_paths(&$objects_array, $append = '')
	{
		$tree = $this->_get_tree();

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