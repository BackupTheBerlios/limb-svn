<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/tree/tree.class.php');
require_once(LIMB_DIR . '/core/model/site_object_factory.class.php');
require_once(LIMB_DIR . '/core/lib/http/uri.class.php');
require_once(LIMB_DIR . '/core/request/request.class.php');
require_once(LIMB_DIR . '/core/model/access_policy.class.php');

class fetcher
{
  var $_custom_class_loaders = array();

  var $_node_mapped_by_request = null;

  var $_cached_objects = array('path' => array(), 'node_id' => array(), 'id' => array());

  var $_is_jip_enabled = false;

  function fetcher()
  {
  }

  function &instance()
  {
    $obj =&	instantiate_object('fetcher');
    return $obj;
  }

  function & _get_access_policy()
  {
    include_once(LIMB_DIR . '/core/model/access_policy.class.php');
    $access_policy =& access_policy :: instance();
    return $access_policy;
  }

  function is_jip_enabled()
  {
    return $this->_is_jip_enabled;
  }

  function set_jip_status($status=true)
  {
    $prev = $this->_is_jip_enabled;
    $this->_is_jip_enabled = $status;
    return $prev;
  }

  function flush_cache()
  {
    $this->_node_mapped_by_request = null; 
    $this->_cached_objects = array('path' => array(), 'node_id' => array(), 'id' => array());
  }

  function _place_object_to_cache(&$object_data, $cache_type='auto', $cache_id='')
  {
    if($cache_type == 'auto')
    {
      $this->_cached_objects['path'][$object_data['path']] =& $object_data;
      $this->_cached_objects['path'][$object_data['path'] . '/'] =& $object_data;
      $this->_cached_objects['node_id'][$object_data['node_id']] =& $object_data;
      $this->_cached_objects['id'][$object_data['id']] =& $object_data;
    }
    else
      $this->_cached_objects[$cache_type][$cache_id] =& $object_data;
  }

  function & _get_object_from_cache($cache_type, $cache_id)
  {
    if(isset($this->_cached_objects[$cache_type][$cache_id]))
      return $this->_cached_objects[$cache_type][$cache_id];
  }

  function _assign_actions(&$objects_data)
  {
    if ($this->is_jip_enabled())
    {
      $access_policy =& $this->_get_access_policy();
      $access_policy->assign_actions_to_objects($objects_data);
    }
  }

  function & fetch($loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch')
  {
    $counter = 0;
    $count_method = $fetch_method . '_count';

    $site_object =& site_object_factory :: create($loader_class_name);
    $counter = $site_object->$count_method($params);

    $result =& $site_object->$fetch_method($params);

    if(!count($result))
      return array();

    $this->_assign_actions($result);

    $this->_assign_paths($result);
    return $result;
  }

  function & fetch_sub_branch($path, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
  {
    $tree =& tree :: instance();
    $site_object =& site_object_factory :: create($loader_class_name);

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

    $objects_data =& $this->fetch_by_ids($object_ids, $loader_class_name, $counter, $params, $fetch_method);

    return $objects_data;
  }

  function & fetch_by_ids($object_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
  {
    $counter = 0;
    $count_method = $fetch_method . '_count';

    $site_object =& site_object_factory :: create($loader_class_name);

    $counter = $site_object->$count_method($object_ids, $params);

    $result =& $site_object->$fetch_method($object_ids, $params);

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
        $ids_sorted_result[$id] =& $result[$id];
    }

    return $ids_sorted_result;
  }

  function & fetch_by_node_ids($node_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
  {
    $object_ids = array();
    $tree =& tree :: instance();

    $nodes = $tree->get_nodes_by_ids($node_ids);
    if (!is_array($nodes) || !count($nodes))
      return array();

    foreach($nodes as $node)
      $object_ids[$node['id']] = $node['object_id'];

    $objects_data =& $this->fetch_by_ids($object_ids, $loader_class_name, $counter, $params, $fetch_method);
    $sorted_objects_data = array();

    foreach($node_ids as $node_id)
    {
      if (!isset($object_ids[$node_id]))
        continue;

      if (isset($objects_data[$object_ids[$node_id]]))
        $sorted_objects_data[$node_id] =& $objects_data[$object_ids[$node_id]];
    }

    return $sorted_objects_data;
  }

  function & fetch_one_by_id($object_id)
  {
    if($object_data =& $this->_get_object_from_cache('id', $object_id))
      return $object_data;

    $access_policy =& $this->_get_access_policy();
    $object_ids = $access_policy->get_accessible_objects(array($object_id));

    if (!is_array($object_ids) || !count($object_ids))
      return false;

    $object_id = reset($object_ids);
    if (!$class_name = $this->_get_object_class_name_by_id($object_id))
      return false;

    $site_object =& site_object_factory :: create($class_name);

    $result =& $site_object->fetch_by_ids(array($object_id));

    if (!is_array($result) || !count($result))
      return false;

    $this->_assign_actions($result);

    $this->_assign_paths($result);

    $object_data = reset($result);

    $this->_place_object_to_cache($object_data);

    return $object_data;
  }

  function & fetch_one_by_node_id($node_id)
  {
    if($object_data =& $this->_get_object_from_cache('node_id', $node_id))
      return $object_data;

    $tree =& tree :: instance();

    if (!$node = $tree->get_node($node_id))
      return false;

    $object_data =& $this->fetch_one_by_id($node['object_id']);

    return $object_data;
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
    if($object_data =& $this->_get_object_from_cache('path', $path))
      return $object_data;

    $tree =& tree :: instance();

    if (!$node = $tree->get_node_by_path($path))
      return false;

    $object_data =& $this->fetch_one_by_node_id($node['id']);
    return $object_data;
  }

  function & fetch_requested_object($request=null)
  {
    if($request === null)
      $request = request :: instance();

    if(!$node =& $this->map_request_to_node())
      return array();

    $prev_jip_status	= $this->set_jip_status(true);

    $object_data =& $this->fetch_one_by_node_id($node['id']);

    $this->set_jip_status($prev_jip_status);

    return $object_data;
  }

  function & map_url_to_node($url, $recursive = false)
  {
    $tree =& tree :: instance();

    $uri = new uri($url);

    if(($node_id = $uri->get_query_item('node_id')) === false)
      $node =& $tree->get_node_by_path($uri->get_path(), '/', $recursive);
    else
      $node =& $tree->get_node((int)$node_id);

    return $node;
  }

  function & map_request_to_node($request = null)
  {
    if($this->_node_mapped_by_request)
      return $this->_node_mapped_by_request;

    if($request === null)
      $request = request :: instance();

    if($node_id = $request->get_attribute('node_id'))
    {
      $tree =& tree :: instance();

      $node =& $tree->get_node((int)$node_id);

      $this->_node_mapped_by_request =& $node;

      return $node;
    }
    else
      $url = $_SERVER['PHP_SELF'];

    $node =& $this->map_url_to_node($url);

    $this->_node_mapped_by_request =& $node;

    return $node;
  }

  function _assign_paths(&$objects_array, $append = '')
  {
    $tree =& tree :: instance();

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

function & map_request_to_node($request = null)
{
  $fetcher =& fetcher :: instance();
  $result =& $fetcher->map_request_to_node($request);
  return $result;
}

function & flush_fetcher_cache()
{
  $fetcher =& fetcher :: instance();
  $fetcher->flush_cache();
}

function & map_url_to_node($url, $recursive = false)
{
  $fetcher =& fetcher :: instance();
  $result =& $fetcher->map_url_to_node($url, $recursive);
  return $result;
}

function & fetch_requested_object()
{
  $fetcher =& fetcher :: instance();
  $result =& $fetcher->fetch_requested_object();
  return $result;
}

function & fetch_one_by_id($object_id)
{
  $fetcher =& fetcher :: instance();
  $result =& $fetcher->fetch_one_by_id($object_id);
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

function & fetch_by_node_ids($node_ids, $loader_class_name, &$counter, $params = array(), $fetch_method = 'fetch_by_ids')
{
  $fetcher =& fetcher :: instance();
  $result =& $fetcher->fetch_by_node_ids($node_ids, $loader_class_name, $counter, $params, $fetch_method);
  return $result;
}

function & wrap_with_site_object($fetched_data)
{
  if(!$fetched_data)
    return false;

  if(!is_array($fetched_data))
    return false;

  if(isset($fetched_data['class_name']))
  {
    $site_object =& site_object_factory :: create($fetched_data['class_name']);
    $site_object->merge_attributes($fetched_data);
    return $site_object;
  }

  $site_objects = array();
  foreach($fetched_data as $id => $data)
  {
    $site_object =& site_object_factory :: create($data['class_name']);
    $site_object->merge_attributes($data);
    $site_objects[$id] =& $site_object;
  }
  return $site_objects;
}
?>
