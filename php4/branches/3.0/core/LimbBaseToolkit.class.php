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

class LimbBaseToolkit// implements LimbToolkit
{
  var $current_dataspace_name = 'default';
  var $fetcher;
  var $response;
  var $request;
  var $session;
  var $user;
  var $db;
  var $tree;
  var $view;
  var $cache;
  var $uow;
  var $ini_cache = array();

  function reset()
  {
    $this->current_dataspace_name = 'default';
    $this->fetcher = null;
    $this->response = null;
    $this->request = null;
    $this->session = null;
    $this->user = null;
    $this->db = null;
    $this->tree = null;
    $this->view = null;
    $this->cache = null;
    $this->uow = null;
    $this->ini_cache = array();
  }

  function define($key, $value)
  {
    define($key, $value);
  }

  function constant($key)
  {
    return constant($key);
  }

  function nextUID()
  {
    include_once(LIMB_DIR . '/core/UIDGenerator.class.php');
    return UIDGenerator :: next();
  }

  function & createDBTable($table_name)
  {
    include_once(LIMB_DIR . '/core/db_tables/LimbDbTableFactory.class.php');
    return LimbDbTableFactory :: create($table_name);
  }

  function & createDAO($dao_path)
  {
    include_once(LIMB_DIR . '/core/dao/DAOFactory.class.php');
    return DAOFactory :: create($dao_path);
  }

  function & createObject($object_path)
  {
    include_once(LIMB_DIR . '/core/ObjectFactory.class.php');
    return ObjectFactory :: create($object_path);
  }

  function & createDataMapper($mapper_path)
  {
    include_once(LIMB_DIR . '/core/data_mappers/DataMapperFactory.class.php');
    return DataMapperFactory :: create($mapper_path);
  }

  function & createBehaviour($behaviour_path)
  {
    include_once(LIMB_DIR . '/core/behaviours/BehaviourFactory.class.php');
    return BehaviourFactory :: create($behaviour_path);
  }

  function & getDbConnection()
  {
    if(is_object($this->db))
      return $this->db;

    include_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
    $this->db =& LimbDbPool :: getConnection();

    return $this->db;
  }

  function & getTree()
  {
    if(is_object($this->tree))
      return $this->tree;

    include_once(LIMB_DIR . '/core/tree/TreeDecorator.class.php');
    include_once(LIMB_DIR . '/core/tree/MaterializedPathTree.class.php');

    $this->tree = new TreeDecorator(new MaterializedPathTree());

    return $this->tree;
  }

  function & getUser()
  {
    if(is_object($this->user))
      return $this->user;

    include_once(LIMB_DIR . '/core/permissions/User.class.php');
    $this->user =& User :: instance();

    return $this->user;
  }

  function & getINI($ini_path)
  {
    if(isset($this->ini_cache[$ini_path]))
      return $this->ini_cache[$ini_path];

    include_once(LIMB_DIR . '/core/util/ini_support.inc.php');

    $ini = getIni($ini_path);

    $this->ini_cache[$ini_path] = $ini;

    return $ini;
  }

  function flushINIcache($ini_path = null)
  {
    if(is_null($ini_path))
      $this->ini_cache = array();
    elseif(isset($this->ini_cache[$ini_path]))
      unset($this->ini_cache[$ini_path]);
  }

  function & getUOW()
  {
    if(is_object($this->uow))
      return $this->uow;

    include_once(LIMB_DIR . '/core/UnitOfWork.class.php');

    $this->uow = new UnitOfWork();

    return $this->uow;
  }


  function & getRequest()
  {
    if(is_object($this->request))
      return $this->request;

    include_once(LIMB_DIR . '/core/request/Request.class.php');
    $this->request = new Request();

    return $this->request;
  }

  function & getResponse()
  {
    if(is_object($this->response))
      return $this->response;

    include_once(LIMB_DIR . '/core/request/HttpResponse.class.php');
    $this->response = new HttpResponse();

    return $this->response;
  }

  function & getCache()
  {
    if(is_object($this->cache))
      return $this->cache;

    include_once(LIMB_DIR . '/core/cache/CacheRegistry.class.php');
    $this->cache = new CacheRegistry();

    return $this->cache;
  }

  function & getLocale($locale_id = '')
  {
    include_once(LIMB_DIR . '/core/i18n/Locale.class.php');
    return Locale :: instance($locale_id);
  }

  function & getSession()
  {
    if(is_object($this->session))
      return $this->session;

    include_once(LIMB_DIR . '/core/session/Session.class.php');
    include_once(LIMB_DIR . '/core/session/SessionDbDriver.class.php');
    $this->session = new Session(new SessionDbDriver());

    return $this->session;
  }

  function & getDataspace()
  {
    include_once(LIMB_DIR . '/core/DataspaceRegistry.class.php');
    return DataspaceRegistry :: get($this->current_dataspace_name);
  }

  function & switchDataspace($name)
  {
    include_once(LIMB_DIR . '/core/DataspaceRegistry.class.php');

    $this->current_dataspace_name = $name;

    return DataspaceRegistry :: get($name);
  }

  function setView(&$view)
  {
    $this->view =& $view;
  }

  function & getView()
  {
    $this->view =& Handle :: resolve($this->view);
    return $this->view;
  }


}

?>