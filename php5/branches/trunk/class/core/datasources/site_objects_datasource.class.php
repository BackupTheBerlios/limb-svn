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
require_once(dirname(__FILE__) . '/datasource.interface.php');
require_once(dirname(__FILE__) . '/countable.interface.php');
require_once(dirname(__FILE__) . '/site_objects_datasource_support.inc.php');

class site_objects_datasource implements datasource, countable
{
  const CACHE_GROUP = 'site_objects';
  
  protected $object_ids;
  protected $behaviours;
  protected $accessible_object_ids;
  protected $site_object_class_name;
  protected $fetch_method;
  protected $limit;
  protected $offset;
  protected $order;
  protected $permissions_action;
  protected $restrict_by_class;
  protected $raw_sql_params;
  protected $site_object;
  
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

  function set_site_object_class_name($name)
  {
    $this->site_object_class_name = $name;
  }

  function set_fetch_method($fetch_method)
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

  function set_restrict_by_class($status = true)
  {
    $this->restrict_by_class = $status;
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
    $this->site_object_class_name = 'site_object';
    $this->fetch_method = 'fetch_by_ids';
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
    
    if ($this->restrict_by_class)
    {
      $params['conditions'][] = ' AND sso.class_id = ' . $this->_get_site_object()->get_class_id();
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

  protected function _get_site_object()
  {
		if ($this->site_object)
		  return $this->site_object;
    
		$this->site_object = Limb :: toolkit()->createSiteObject($this->site_object_class_name);
 
    return $this->site_object;
  }
  
  public function count_total()
  {
		if (!$object_ids = $this->get_accessible_object_ids())
		  return 0;
    
    $sql_params = $this->_collect_raw_sql_params();
    $count_method = $this->fetch_method . '_count';
    
    $key = array($object_ids, $sql_params, $count_method);
    $cache = Limb :: toolkit()->getCache();
    
    $result = $cache->get($key, self :: CACHE_GROUP);
    
    if($result !== null)
      return $result; 
        
    $site_object = $this->_get_site_object();
    
    $result = $site_object->$count_method($object_ids, $sql_params);
    
    $cache->put($key, $result, self :: CACHE_GROUP);
    
    return $result;
  }  
    
  public function fetch()
  {
		if (!$object_ids = $this->get_accessible_object_ids())
		  return array();
    
    $params = $this->_collect_params();
    $sql_params = $this->_collect_raw_sql_params();
    $fetch_method = $this->fetch_method;
    
    $key = array($object_ids, $params, $sql_params, $fetch_method);
    $cache = Limb :: toolkit()->getCache();
    
    $result = $cache->get($key, self :: CACHE_GROUP);
    
    if($result !== null)
      return $result; 

		$site_object = $this->_get_site_object();
    
		$result = $site_object->$fetch_method($object_ids, 
                                          $params, 
                                          $sql_params);
    
    $cache->put($key, $result, self :: CACHE_GROUP);
    
    return $result; 
  }
  
  public function flush_cache()
  {
    Limb :: toolkit()->getCache()->flush(self :: CACHE_GROUP);
  }
  
  protected function _get_behaviours_ids()
  {
    require_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');
    return site_object_behaviour :: get_ids_by_names($this->behaviours);
  }  
}

?> 