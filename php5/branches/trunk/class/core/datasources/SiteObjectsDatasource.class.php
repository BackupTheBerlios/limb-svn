<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/datasource.interface.php');
require_once(dirname(__FILE__) . '/countable.interface.php');
require_once(dirname(__FILE__) . '/site_objects_datasource_support.inc.php');

class site_objects_datasource implements datasource, countable
{
  const CACHE_GROUP = 'site_objects';

  protected $object_ids;
  protected $behaviours;
  protected $accessible_object_ids;
  protected $finder_name;
  protected $fetch_method;
  protected $limit;
  protected $offset;
  protected $order;
  protected $permissions_action;
  protected $restriction_class_name;
  protected $raw_sql_params;
  protected $finder;

  function __construct()
  {
    $this->reset();
  }

  function set_object_ids($object_ids)
  {
    $this->object_ids = $object_ids;
  }

  function set_behaviours($behaviours)
  {
    $this->behaviours = $behaviours;
  }

  function set_finder_name($name)
  {
    $this->finder_name = $name;
  }

  function set_find_method($fetch_method)
  {
    $this->fetch_method = $fetch_method;
  }

  function set_limit($limit)
  {
    $this->limit = $limit;
  }

  function set_offset($offset)
  {
    $this->offset = $offset;
  }

  function set_order($order)
  {
    $this->order = $order;
  }

  function set_restriction_class_name($class_name)
  {
    $this->restriction_class_name = $class_name;
  }

  function set_raw_sql_params($params)
  {
    $this->raw_sql_params = $params;
  }

  function set_permissions_action($permissions_action)
  {
    $this->permissions_action = $permissions_action;
  }

  function reset()
  {
    $this->object_ids = array();
    $this->behaviours = array();
    $this->accessible_object_ids = array();
    $this->finder_name = 'site_objects_raw_finder';
    $this->fetch_method = 'find';
    $this->limit = null;
    $this->offset = null;
    $this->order = null;
    $this->permissions_action = 'display';
    $this->restrict_by_class = false;
    $this->raw_sql_params = array();
    $this->site_object = null;
  }

  protected function _collect_params()
  {
    $params = array();
    $params['limit'] = $this->limit;
    $params['offset'] = $this->offset;
    $params['order'] = $this->order;

    return $params;
  }

  protected function _collect_raw_sql_params()
  {
    $params = $this->raw_sql_params;

    if ($object_ids = $this->get_accessible_object_ids())
      $params['conditions'][] = ' AND ' . sql_in('sso.id', $object_ids);

    if ($this->restriction_class_name)
    {
      $params['conditions'][] = ' AND sys_class.name = ' . $this->restriction_class_name;
    }

    if ($this->behaviours)
    {
      $params['conditions'][] = ' AND ' . sql_in('sso.behaviour_id', $this->_get_behaviours_ids());
    }

    return $params;
  }


  public function get_object_ids()
  {
    return $this->object_ids;
  }

  public function get_accessible_object_ids()
  {
    $cache = Limb :: toolkit()->getCache();

    $ids = $this->get_object_ids();
    $action = $this->permissions_action;
    $key = array($ids, $action);

    $result = $cache->get($key, self :: CACHE_GROUP);

    if($result !== null)
      return $result;

    $authorizer = Limb :: toolkit()->getAuthorizer();
    $result = $authorizer->get_accessible_object_ids($ids, $action);

    $cache->put($key, $result, self :: CACHE_GROUP);

    return $result;
  }

  protected function _get_finder()
  {
    if ($this->finder)
      return $this->finder;

    include_once(LIMB_DIR . '/class/core/finders/finder_factory.class.php');

    $this->finder = finder_factory :: create($this->finder_name);

    return $this->finder;
  }

  public function count_total()
  {
    $sql_params = $this->_collect_raw_sql_params();
    $count_method = $this->fetch_method . '_count';

    $key = array($sql_params, $count_method);
    $cache = Limb :: toolkit()->getCache();

    $result = $cache->get($key, self :: CACHE_GROUP);

    if($result !== null)
      return $result;

    $finder = $this->_get_finder();

    $result = $finder->$count_method($sql_params);

    $cache->put($key, $result, self :: CACHE_GROUP);

    return $result;
  }

  public function fetch()
  {
    $params = $this->_collect_params();
    $sql_params = $this->_collect_raw_sql_params();
    $fetch_method = $this->fetch_method;

    $key = array($params, $sql_params, $fetch_method);
    $cache = Limb :: toolkit()->getCache();

    $result = $cache->get($key, self :: CACHE_GROUP);

    if($result !== null)
      return $result;

    $finder = $this->_get_finder();

    if (!method_exists($finder, $fetch_method))
      throw new LimbException($fetch_method .' is not supported by finder');

    $result = $finder->$fetch_method($params, $sql_params);

    $cache->put($key, $result, self :: CACHE_GROUP);

    return $result;
  }

  public function flush_cache()
  {
    Limb :: toolkit()->getCache()->flush(self :: CACHE_GROUP);
  }

  protected function _get_behaviours_ids()
  {
    require_once(LIMB_DIR . '/class/core/data_mappers/site_object_behaviour_mapper.class.php');
    return site_object_behaviour_mapper :: get_ids_by_names($this->behaviours);
  }
}

?>