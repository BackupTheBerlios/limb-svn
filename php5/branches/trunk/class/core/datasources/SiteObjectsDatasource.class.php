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
require_once(dirname(__FILE__) . '/Datasource.interface.php');
require_once(dirname(__FILE__) . '/Countable.interface.php');
require_once(dirname(__FILE__) . '/site_objects_datasource_support.inc.php');

class SiteObjectsDatasource implements Datasource, Countable
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

  function setObjectIds($object_ids)
  {
    $this->object_ids = $object_ids;
  }

  function setBehaviours($behaviours)
  {
    $this->behaviours = $behaviours;
  }

  function setFinderName($name)
  {
    $this->finder_name = $name;
  }

  function setFindMethod($fetch_method)
  {
    $this->fetch_method = $fetch_method;
  }

  function setLimit($limit)
  {
    $this->limit = $limit;
  }

  function setOffset($offset)
  {
    $this->offset = $offset;
  }

  function setOrder($order)
  {
    $this->order = $order;
  }

  function setRestrictionClassName($class_name)
  {
    $this->restriction_class_name = $class_name;
  }

  function setRawSqlParams($params)
  {
    $this->raw_sql_params = $params;
  }

  function setPermissionsAction($permissions_action)
  {
    $this->permissions_action = $permissions_action;
  }

  function reset()
  {
    $this->object_ids = array();
    $this->behaviours = array();
    $this->accessible_object_ids = array();
    $this->finder_name = 'SiteObjectsRawFinder';
    $this->fetch_method = 'find';
    $this->limit = null;
    $this->offset = null;
    $this->order = null;
    $this->permissions_action = 'display';
    $this->restrict_by_class = false;
    $this->raw_sql_params = array();
    $this->site_object = null;
  }

  protected function _collectParams()
  {
    $params = array();
    $params['limit'] = $this->limit;
    $params['offset'] = $this->offset;
    $params['order'] = $this->order;

    return $params;
  }

  protected function _collectRawSqlParams()
  {
    $params = $this->raw_sql_params;

    if ($object_ids = $this->getAccessibleObjectIds())
      $params['conditions'][] = ' AND ' . sqlIn('sso.id', $object_ids);

    if ($this->restriction_class_name)
    {
      $params['conditions'][] = ' AND sys_class.name = ' . $this->restriction_class_name;
    }

    if ($this->behaviours)
    {
      $params['conditions'][] = ' AND ' . sqlIn('sso.behaviour_id', $this->_getBehavioursIds());
    }

    return $params;
  }


  public function getObjectIds()
  {
    return $this->object_ids;
  }

  public function getAccessibleObjectIds()
  {
    $cache = Limb :: toolkit()->getCache();

    $ids = $this->getObjectIds();
    $action = $this->permissions_action;
    $key = array($ids, $action);

    $result = $cache->get($key, self :: CACHE_GROUP);

    if($result !== null)
      return $result;

    $authorizer = Limb :: toolkit()->getAuthorizer();
    $result = $authorizer->getAccessibleObjectIds($ids, $action);

    $cache->put($key, $result, self :: CACHE_GROUP);

    return $result;
  }

  protected function _getFinder()
  {
    if ($this->finder)
      return $this->finder;

    include_once(LIMB_DIR . '/class/core/finders/FinderFactory.class.php');

    $this->finder = FinderFactory :: create($this->finder_name);

    return $this->finder;
  }

  public function countTotal()
  {
    $sql_params = $this->_collectRawSqlParams();
    $count_method = $this->fetch_method . 'Count';

    $key = array($sql_params, $count_method);
    $cache = Limb :: toolkit()->getCache();

    $result = $cache->get($key, self :: CACHE_GROUP);

    if($result !== null)
      return $result;

    $finder = $this->_getFinder();

    $result = $finder->$count_method($sql_params);

    $cache->put($key, $result, self :: CACHE_GROUP);

    return $result;
  }

  public function fetch()
  {
    $params = $this->_collectParams();
    $sql_params = $this->_collectRawSqlParams();
    $fetch_method = $this->fetch_method;

    $key = array($params, $sql_params, $fetch_method);
    $cache = Limb :: toolkit()->getCache();

    $result = $cache->get($key, self :: CACHE_GROUP);

    if($result !== null)
      return $result;

    $finder = $this->_getFinder();

    if (!method_exists($finder, $fetch_method))
      throw new LimbException($fetch_method .' is not supported by finder');

    $result = $finder->$fetch_method($params, $sql_params);

    $cache->put($key, $result, self :: CACHE_GROUP);

    return $result;
  }

  public function flushCache()
  {
    Limb :: toolkit()->getCache()->flush(self :: CACHE_GROUP);
  }

  protected function _getBehavioursIds()
  {
    require_once(LIMB_DIR . '/class/core/data_mappers/SiteObjectBehaviourMapper.class.php');
    return SiteObjectBehaviourMapper :: getIdsByNames($this->behaviours);
  }
}

?>