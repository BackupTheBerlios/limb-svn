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
require_once(LIMB_DIR . 'class/template/component.class.php');
require_once(LIMB_DIR . 'class/db_tables/db_table_factory.class.php');
require_once(LIMB_DIR . 'class/core/fetcher.class.php');
require_once(LIMB_DIR . '/class/core/array_dataset.class.php');

class metadata_component extends component
{
	protected $node_id = '';
	
	protected $request_path = '';

	protected $object_ids_array = array();

	protected $object_metadata = array();

	protected $separator = ' - ';

	protected $offset_path = '';

	protected $metadata_db_table_name = 'sys_metadata';
	protected $needed_metadata = array('keywords', 'description');

	protected function _get_path_objects_ids_array()
	{
		if (count($this->object_ids_array))
			return $this->object_ids_array;

		$tree = Limb :: toolkit()->getTree();

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

		return $this->object_ids_array = $result;
	}

	protected function  _get_path_objects_array()
	{
		$ids_array = $this->_get_path_objects_ids_array();

		$sql =
		'SELECT
		sso.id as id,
		sso.class_id as class_id,
		sso.current_version as current_version,
		sso.modified_date as modified_date,
		sso.identifier as identifier,
		sso.status as status,
		sso.created_date as created_date,
		sso.creator_id as creator_id,
		sso.locale_id as locale_id,
		sso.title as title
		FROM sys_site_object as sso, sys_site_object_tree as ssot
		WHERE ssot.object_id=sso.id
		AND	' . sql_in('sso.id', $ids_array) . '
		ORDER BY ssot.level';

		$db = db_factory :: instance();
		$db->sql_exec($sql);

		return $db->get_array('id');
	}

	public function load_metadata()
	{
		$ids_array = $this->_get_path_objects_ids_array();

		if (!count($ids_array))
			return false;
		$ids_array = array_reverse($ids_array);

		$metadata_db_table	= Limb :: toolkit()->createDBTable($this->metadata_db_table_name);
		$objects_metadata = $metadata_db_table->get_list(sql_in('object_id', $ids_array), '', 'object_id');

		if (!count($objects_metadata))
			return false;

		$this->_process_loaded_metadata($ids_array, $objects_metadata);

		return true;
	}

	protected function _process_loaded_metadata($ids_array, $objects_metadata)
	{
		foreach($this->needed_metadata as $metadata_name)
			$metadata_loaded[$metadata_name] = false;

		foreach($ids_array as $object_id)
		{
			$can_stop_search = true;
			foreach($this->needed_metadata as $metadata_name)
				$can_stop_search = $can_stop_search && $metadata_loaded[$metadata_name];

			if ($can_stop_search)
				break;

			foreach($this->needed_metadata as $metadata_name)
				if (!$metadata_loaded[$metadata_name] && !empty($objects_metadata[$object_id][$metadata_name]))
				{
					$this->object_metadata[$metadata_name] = $objects_metadata[$object_id][$metadata_name];
					$metadata_loaded[$metadata_name] = true;
				}
		}
	}

	public function set_node_id($node_id)
	{
		$this->node_id = $node_id;
	}

	public function get_node_id()
	{
    $toolkit = Limb :: toolkit();
    
		if (!$this->node_id)
		{
      $request = $toolkit->getRequest();
      
			if($this->request_path)
			{
			  $node_path = $request->get($this->request_path);

				if(!$node = $toolkit->getFetcher()->map_uri_to_node(new uri($node_path)))
					$node = $toolkit->getFetcher()->map_request_to_node($request);
			}
			else
				$node = $toolkit->getFetcher()->map_request_to_node($request);

			$this->node_id = $node['id'];
		}

		return $this->node_id;
	}

	public function get_keywords()
	{
		return $this->get('keywords');
	}

	public function get_description()
	{
		return $this->get('description');
	}

  public function get($name, $default_value = null)
  {
		if(isset($this->object_metadata[$name]))
			return $this->object_metadata[$name];
		else
			return $default_value;
  }

	public function set_title_separator($separator = ' ')
	{
		$this->separator = $separator;
	}

	public function get_title()
	{
		$result = $this->_apply_offset_path($this->_get_path_objects_array());

		if (!is_array($result) || !count($result))
			return null;

		$titles = array();

		$objects_ids_array = array_reverse($this->_get_path_objects_ids_array());
		foreach($objects_ids_array as $object_id)
			if (!empty($result[$object_id]['title']))
				$titles[] = $result[$object_id]['title'];

		if (!count($titles))
			return null;

		return implode($this->separator, $titles);
	}

	public function get_breadcrumbs_dataset()
	{
		$objects_data = $this->_get_path_objects_array();

		if (!is_array($objects_data) || !count($objects_data))
			return new array_dataset();

		$results = $this->_apply_offset_path($objects_data);

		$this->_add_object_action_path($results);

		$record = end($results);
		array_pop($results);

		$record['is_last'] = true;
		$results[-1] = $record;//???

		return new array_dataset($results);
	}

	protected function _apply_offset_path($objects_data)
	{
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
			$results[$data['id']] = array(
			  'id' => $data['id'],
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

			$last_element = $data['id'];
		}

		return $results;
	}

	protected function _add_object_action_path(&$results)
	{
		$data = end($results);
		$path = $data['path'];

		$controller = $this->_get_mapped_controller();
    $request = Limb :: toolkit()->getRequest();
		$action = $controller->get_action($request);

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

	protected function _get_mapped_controller()
	{
    $request = Limb :: toolkit()->getRequest();
	  return wrap_with_site_object(Limb :: toolkit()->getFetcher()->fetch_requested_object($request))->get_controller();
	}

	public function set_offset_path($path)
	{
		$this->offset_path = $path;
	}

	public function set_request_path($path)
	{
		$this->request_path = $path;
	}
}

?>