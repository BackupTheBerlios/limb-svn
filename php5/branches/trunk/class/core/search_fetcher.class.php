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
require_once(LIMB_DIR . 'class/core/fetcher.class.php');
require_once(LIMB_DIR . 'class/search/full_text_search.class.php');

class search_fetcher extends fetcher
{
	protected $_query_object = null;

	static public function instance()
	{
    if (!self :: $_instance)
      self :: $_instance = new search_fetcher();

    return self :: $_instance;
	}

	public function set_search_query_object($query_object)
	{
		$this->_query_object = $query_object;
	}

	protected function _get_classes_ids_from_string($classes_string)
	{
		$classes_ids = array();
		$classes_names = explode(',', $classes_string);
		foreach($classes_names as $class_name)
		{
			if(trim($class_name))
			{
				$site_object = Limb :: toolkit()->createSiteObject(trim($class_name));
				$classes_ids[] = $site_object->get_class_id();
			}
		}

		return $classes_ids;
	}

	public function search_fetch($loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_accessible_by_ids')
	{
		if (!$this->_query_object)
    	return array();

		$site_object = Limb :: toolkit()->createSiteObject($loader_class_name);

		$restricted_classes = array();
		$allowed_classes = array();

		if (!isset($params['restrict_by_class']) ||
				(isset($params['restrict_by_class']) && (bool)$params['restrict_by_class']))
			$class_id = $site_object->get_class_id();
		else
		{
			$class_id = null;

			if(isset($params['restricted_classes']))
				$restricted_classes = $this->_get_classes_ids_from_string($params['restricted_classes']);
			if(isset($params['allowed_classes']))
				$allowed_classes = $this->_get_classes_ids_from_string($params['allowed_classes']);

		}

		$search = new full_text_search();

		$search_result = $search->find($this->_query_object, $class_id, $restricted_classes, $allowed_classes);
		if (!count($search_result))
			return array();

		$counter = 0;
		$count_method = $fetch_method . '_count';

		$counter = $site_object->$count_method(array_keys($search_result), $params);
		$fetched_objects = $site_object->$fetch_method(array_keys($search_result), $params);

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

		Limb :: toolkit()->getAuthorizer()->assign_actions_to_objects($result);

		$this->_assign_paths($result);
		$this->_assign_search_paths($result, isset($params['offset']) ? $params['offset'] : 0);

		return $result;
	}

	public function search_fetch_sub_branch($path, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch')
	{
		$tree = Limb :: toolkit()->getTree();
		$site_object = Limb :: toolkit()->createSiteObject($loader_class_name);

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

		return $this->search_fetch_by_ids($object_ids, $loader_class_name, $counter, $params, $fetch_method);
	}

	public function search_fetch_by_ids($object_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
	{
		if (!$this->_query_object)
    	return array();

		$search = new full_text_search();
		$search_result = $search->find_by_ids($object_ids, $this->_query_object);

		if(!count($search_result))
			return array();

		$counter = 0;
		$count_method = $fetch_method . '_count';

		$site_object = Limb :: toolkit()->createSiteObject($loader_class_name);
		$counter = $site_object->$count_method(array_keys($search_result), $params);
		$fetched_objects = $site_object->$fetch_method(array_keys($search_result),$params);

		if(!count($fetched_objects))
			return array();

		foreach($search_result as $key => $score)
			if (isset($fetched_objects[$key]))
			{
				$result[$key] = $fetched_objects[$key];
				$result[$key]['score'] = $score;
			}

		Limb :: toolkit()->getAuthorizer()->assign_actions_to_objects($result);

		$this->_assign_paths($result);
		$this->_assign_search_paths($result, isset($params['offset']) ? $params['offset'] : 0);

		return $result;
	}

	protected function _assign_search_paths(& $objects_array, $offset = 0)
	{
		$query = $this->_query_object->to_string();

		foreach($objects_array as $key => $data)
		{
			if(!isset($objects_array[$key]['title']) || !$objects_array[$key]['title'])
				$objects_array[$key]['title'] = $objects_array[$key]['path'];

			$objects_array[$key]['search_path'] = $objects_array[$key]['path'] . '?h=' . urlencode($query);
			$objects_array[$key]['search_full_path'] = 'http://' . $_SERVER['HTTP_HOST'] . $objects_array[$key]['path'];
		}
	}
}

?>