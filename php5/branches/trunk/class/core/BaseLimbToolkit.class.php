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
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');

class BaseLimbToolkit implements LimbToolkit
{
  protected $current_dataspace_name = 'default';
  protected $fetcher;
  protected $authorizer;
  protected $authenticator;
  protected $response;
  protected $request;
  protected $session;
  protected $user;
  protected $db;
  protected $tree;
  protected $view;
  protected $cache;
  protected $ini_cache = array();

  public function define($key, $value)
  {
    define($key, $value);
  }

  public function constant($key)
  {
    return constant($key);
  }

  public function createDBTable($table_name)
  {
    include_once(LIMB_DIR . '/class/db_tables/DbTableFactory.class.php');
    return DbTableFactory :: create($table_name);
  }

  public function getDatasource($datasource_path)
  {
    include_once(LIMB_DIR . '/class/core/datasources/DatasourceFactory.class.php');
    return DatasourceFactory :: create($datasource_path);
  }

  public function createSiteObject($site_object_path)
  {
    include_once(LIMB_DIR . '/class/core/site_objects/SiteObjectFactory.class.php');
    return SiteObjectFactory :: create($site_object_path);
  }

  public function createDataMapper($mapper_path)
  {
    include_once(LIMB_DIR . '/class/core/data_mappers/DataMapperFactory.class.php');
    return DataMapperFactory :: create($mapper_path);
  }

  public function createBehaviour($behaviour_path)
  {
    include_once(LIMB_DIR . '/class/core/behaviours/SiteObjectBehaviourFactory.class.php');
    return SiteObjectBehaviourFactory :: create($behaviour_path);
  }

  public function getDB()
  {
    if($this->db)
      return $this->db;

    include_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
    $this->db = DbFactory :: instance();

    return $this->db;
  }

  public function getTree()
  {
    if($this->tree)
      return $this->tree;

    include_once(LIMB_DIR . '/class/core/tree/TreeDecorator.class.php');
    include_once(LIMB_DIR . '/class/core/tree/MaterializedPathTree.class.php');

    $this->tree = new TreeDecorator(new MaterializedPathTree());

    return $this->tree;
  }

  public function getUser()
  {
    if($this->user)
      return $this->user;

    include_once(LIMB_DIR . '/class/core/permissions/User.class.php');
    $this->user = User :: instance();

    return $this->user;
  }

  public function getINI($ini_path)
  {
    if(isset($this->ini_cache[$ini_path]))
      return $this->ini_cache[$ini_path];

    include_once(LIMB_DIR . '/class/lib/util/ini_support.inc.php');

    $ini = getIni($ini_path);

    $this->ini_cache[$ini_path] = $ini;

    return $ini;
  }

  public function flushINIcache()
  {
    $this->ini_cache = array();
  }

  public function getAuthenticator()
  {
    if($this->authenticator)
      return $this->authenticator;

    include_once(LIMB_SIMPLE_PERMISSIONS_DIR . '/SimpleAuthenticator.class.php');

    $this->authenticator = new SimpleAuthenticator();

    return $this->authenticator;
  }

  public function getAuthorizer()
  {
    if($this->authorizer)
      return $this->authorizer;

    include_once(LIMB_SIMPLE_PERMISSIONS_DIR . '/SimpleAuthorizer.class.php');

    $this->authorizer = new SimpleAuthorizer();

    return $this->authorizer;
  }

  public function getRequest()
  {
    if($this->request)
      return $this->request;

    include_once(LIMB_DIR . '/class/core/request/Request.class.php');
    $this->request = new Request();

    return $this->request;
  }

  public function getResponse()
  {
    if($this->response)
      return $this->response;

    include_once(LIMB_DIR . '/class/core/request/HttpResponse.class.php');
    $this->response = new HttpResponse();

    return $this->response;
  }

  public function getCache()
  {
    if($this->cache)
      return $this->cache;

    include_once(LIMB_DIR . '/class/cache/CacheRegistry.class.php');
    $this->cache = new CacheRegistry();

    return $this->cache;
  }

  public function getLocale($locale_id = '')
  {
    include_once(LIMB_DIR . '/class/i18n/Locale.class.php');
    return Locale :: instance($locale_id);
  }

  public function getSession()
  {
    if($this->session)
      return $this->session;

    include_once(LIMB_DIR . '/class/core/session/Session.class.php');
    include_once(LIMB_DIR . '/class/core/session/SessionDbDriver.class.php');
    $this->session = new Session(new SessionDbDriver());

    return $this->session;
  }

  public function getDataspace()
  {
    include_once(LIMB_DIR . '/class/core/DataspaceRegistry.class.php');
    return DataspaceRegistry :: get($this->current_dataspace_name);
  }

  public function switchDataspace($name)
  {
    include_once(LIMB_DIR . '/class/core/DataspaceRegistry.class.php');

    $this->current_dataspace_name = $name;

    return DataspaceRegistry :: get($name);
  }

  public function setView($view)
  {
    $this->view = $view;
  }

  public function getView()
  {
    resolveHandle($this->view);
    return $this->view;
  }


}

?>
