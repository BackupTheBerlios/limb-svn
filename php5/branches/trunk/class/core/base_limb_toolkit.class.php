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
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');

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
    include_once(LIMB_DIR . '/class/db_tables/db_table_factory.class.php');
    return db_table_factory :: create($table_name);
  }
  
  public function getDatasource($datasource_path)
  {
    include_once(LIMB_DIR . '/class/core/datasources/datasource_factory.class.php');
    return datasource_factory :: create($datasource_path);    
  }
  
  public function createSiteObject($site_object_path)
  {
    include_once(LIMB_DIR . '/class/core/site_objects/site_object_factory.class.php');
    return site_object_factory :: create($site_object_path);    
  }
  
  public function createBehaviour($behaviour_path)
  {
    include_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour_factory.class.php');
    return site_object_behaviour_factory :: create($behaviour_path);       
  }
  
  public function getDB()
  {
    if($this->db)
      return $this->db;
    
    include_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
    $this->db = db_factory :: instance();
    
    return $this->db;
  }
  
  public function getTree()
  {
    if($this->tree)
      return $this->tree;
    
    include_once(LIMB_DIR . '/class/core/tree/tree_decorator.class.php');
		include_once(LIMB_DIR . '/class/core/tree/materialized_path_tree.class.php');
    
    $this->tree = new tree_decorator(new materialized_path_tree());
    
    return $this->tree;
  }
    
  public function getUser()
  {
    if($this->user)
      return $this->user;
    
    include_once(LIMB_DIR . '/class/core/permissions/user.class.php');
    $this->user = user :: instance();
    
    return $this->user;
  }
  
  public function getAuthenticator()
  {
    if($this->authenticator)
      return $this->authenticator;
    
    include_once(LIMB_SIMPLE_PERMISSIONS_DIR . '/simple_authenticator.class.php');
    
    $this->authenticator = new simple_authenticator();
    
    return $this->authenticator;
  }
  
  public function getAuthorizer()
  {
    if($this->authorizer)
      return $this->authorizer;
    
    include_once(LIMB_SIMPLE_PERMISSIONS_DIR . '/simple_authorizer.class.php');
    
    $this->authorizer = new simple_authorizer();
    
    return $this->authorizer;
  }
  
  public function getRequest()
  {
    if($this->request)
      return $this->request;
    
    include_once(LIMB_DIR . '/class/core/request/request.class.php');
    $this->request = new request();
    
    return $this->request;
  }
  
  public function getResponse()
  {
    if($this->response)
      return $this->response;

    include_once(LIMB_DIR . '/class/core/request/http_response.class.php');
    $this->response = new http_response();
    
    return $this->response;    
  }  

  public function getCache()
  {
    if($this->cache)
      return $this->cache;

    include_once(LIMB_DIR . '/class/cache/cache_registry.class.php');
    $this->cache = new CacheRegistry();
    
    return $this->cache;    
  }  
  
  public function getLocale($locale_id = '')
  {
    include_once(LIMB_DIR . '/class/i18n/locale.class.php');
    return locale :: instance($locale_id);
  }
  
  public function getSession()
  {
    if($this->session)
      return $this->session;
    
    include_once(LIMB_DIR . '/class/core/session/session.class.php');
    include_once(LIMB_DIR . '/class/core/session/session_db_driver.class.php');    
    $this->session = new session(new session_db_driver());
    
    return $this->session;    
  }    
  
  public function getDataspace()
  {
    include_once(LIMB_DIR . '/class/core/dataspace_registry.class.php');    
    return dataspace_registry :: get($this->current_dataspace_name);
  }
  
  public function switchDataspace($name)
  {
    include_once(LIMB_DIR . '/class/core/dataspace_registry.class.php');
    
    $this->current_dataspace_name = $name;
    
    return dataspace_registry :: get($name);
  }
  
  public function setView($view)
  {
    $this->view = $view;
  }
  
  public function getView()
  {
    resolve_handle($this->view);
    return $this->view;
  }
 
  
}

?> 
