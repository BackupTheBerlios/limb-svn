<?php

require_once(LIMB_DIR . 'core/tree/limb_tree.class.php');
require_once(LIMB_DIR . 'core/template/component.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_table.class.php');
require_once(LIMB_DIR . 'core/fetcher.class.php');
require_once(LIMB_DIR . '/core/lib/util/array_dataset.class.php');

class metadata_component extends component
{
	var $node_id = '';
	
	var $_object_ids_array = array();

	var $object_metadata = array();
	
	var $separator = ' - ';
	
	var $offset_path = '';
	
	function _get_path_objects_ids_array()
	{		
		if (count($this->_object_ids_array))
			return $this->_object_ids_array;
	
		$tree =& limb_tree :: instance();
		
		$node = $tree->get_node($this->get_node_id());
		$parents = $tree->get_parents($this->get_node_id());

		$result = array();	

		if (is_array($parents) && count($parents))
			foreach($parents as $parent_node)
			{
				$result[$parent_node['object_id']] = $parent_node['object_id'];
			}
		
		if ($node)
			$result[$node['object_id']] = $node['object_id'];
		
		return $this->_object_ids_array = $result;
	}
	
	function & _get_path_objects_array()
	{
		$ids_array = $this->_get_path_objects_ids_array();
				
		$sql = 
		'SELECT sso.* 
		FROM sys_site_object as sso, sys_site_object_tree as ssot
		WHERE ssot.object_id=sso.id 
		AND	' . sql_in('sso.id', $ids_array) . '
		ORDER BY ssot.level';
		
		$db =& db_factory :: instance();
		$db->sql_exec($sql);
		
		$objects_data =& $db->get_array('id');
		return $objects_data;
	}
	
	function load_metadata()
	{
		$ids_array = $this->_get_path_objects_ids_array();
		
		if (!count($ids_array))
			return false;
			
		$sys_metadata_db_table	=& db_table_factory :: instance('sys_metadata');
		$objects_metadata = $sys_metadata_db_table->get_list(sql_in('object_id', $ids_array), '', 'object_id');
		
		if (!count($objects_metadata))		
			return false;
		
		$ids_array = array_reverse($ids_array);
		$got_keywords = false;
		$got_description = false;
		
		foreach($ids_array as $object_id)
		{
			if ($got_keywords && $got_description)
				break;
				
			if (!$got_keywords && !empty($objects_metadata[$object_id]['keywords']))
			{
				$this->object_metadata['keywords'] = $objects_metadata[$object_id]['keywords'];
				$got_keywords = true;
			}	

			if (!$got_description && !empty($objects_metadata[$object_id]['description']))
			{
				$this->object_metadata['description'] = $objects_metadata[$object_id]['description'];
				$got_description = true;
			}	
		}
		
		return true;		
	}
	
	function set_node_id($node_id)
	{
		$this->node_id = $node_id;
	}
	
	function get_node_id()
	{
		if (!$this->node_id)
		{
			$node = map_url_to_node();
			$this->node_id = $node['id'];
		}

		return $this->node_id;
	}
	
	function get_keywords()
	{
		return $this->get('keywords');
	}
	
	function get_description()
	{
		return $this->get('description');
	}

  function get($name)
  {
		if(isset($this->object_metadata[$name]))
			return $this->object_metadata[$name];
		else
			return null;	
  }
		
	function set_title_separator($separator = ' ')
	{
		$this->separator = $separator;
	}
	
	function get_title()
	{	
		$objects_data =& $this->_get_path_objects_array();
		
		if (!is_array($objects_data) || !count($objects_data))
			return null;
			
		$titles = array();
		
		$objects_ids_array = array_reverse($this->_get_path_objects_ids_array());
		foreach($objects_ids_array as $object_id)
			if (!empty($objects_data[$object_id]['title']))
				$titles[] = $objects_data[$object_id]['title'];
		
		if (!count($titles))			
			return null;

		return implode($this->separator, $titles);
	}
	
	function & get_breadcrumbs_dataset()
	{
		$objects_data =& $this->_get_path_objects_array();
		
		if (!is_array($objects_data) || !count($objects_data))
			return new array_dataset();
			
		$path = '/';
		
		if($this->offset_path)
		{
			$offset_arr = explode('/', $this->offset_path);
			reset($offset_arr);
			array_shift($offset_arr);
		}
		
		$results = array();
		foreach($objects_data as $data)
		{			
			$path .= $data['identifier'] . '/';
			$results[] = array(
				'path' => $path,
				'title' => $data['title'] ? $data['title'] : $data['identifier']
			);
			
			if($this->offset_path && current($offset_arr))
			{
				if($data['identifier'] == current($offset_arr))
				{
					array_pop($results);
					next($offset_arr);
				}
			}
		}
		
		$this->_add_object_action_path($results);
		
		$results[sizeof($results)-1]['is_last'] = true;
		
		return new array_dataset($results);
	}
	
	function _add_object_action_path(&$results)
	{
		$data = end($results);
		$path = $data['path'];
		
		$controller =& $this->_get_mapped_controller();
		
		$action = $controller->determine_action();
		
		if ($action !== false && 
				$controller->get_action_property($action, 'display_in_breadcrumbs') === true)
		{
			if($controller->get_default_action() != $action)
			{				
				$results[] = array(
					'path' => $path .= '?action=' . $action,
					'title' => $controller->get_action_name($action)
				);
			}
		}
	}
	
	function &_get_mapped_controller()//for testing
	{
		$controller =& get_mapped_controller();
		return $controller;
	}
			
	function set_offset_path($path)
	{
		$this->offset_path = $path;
	}
	
} 

?>