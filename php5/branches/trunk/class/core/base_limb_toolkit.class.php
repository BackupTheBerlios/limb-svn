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
  protected $fetcher;
  protected $authorizer;
  protected $authenticator;
  protected $response;
  protected $request;
  protected $user;
  protected $db;
  protected $tree;
  
  public function createDBTable($table_name)
  {
    include_once(LIMB_DIR . '/class/db_tables/db_table_factory.class.php');
    return db_table_factory :: create($table_name);
  }
  
  public function createDatasource($datasource_path)
  {
    include_once(LIMB_DIR . '/class/datasources/datasource_factory.class.php');
    return datasource_factory :: create($datasource_path);    
  }
  
  public function createSiteObject($site_object_path)
  {
    include_once(LIMB_DIR . '/class/core/site_objects/site_object_factory.class.php');
    return site_object_factory :: create($site_object_path);    
  }
  
  public function createController($controller_path)
  {
    include_once(LIMB_DIR . '/class/core/controllers/site_object_controller_factory.class.php');
    return site_object_controller_factory :: create($controller_path);       
  }
  
  public function createAction($action_path)
  {
    include_once(LIMB_DIR . '/class/core/actions/action_factory.class.php');
    return action_factory :: create($action_path);           
  }
  
  public function getDB()
  {
    if($this->db)
      return $this->db;
    
    include_once(LIMB_DIR . '/class/core/lib/db/db_factory.class.php');
    $this->db = db_factory :: instance();
    
    return $this->db;
  }
  
  public function getTree()
  {
    if($this->tree)
      return $this->tree;
    
    include_once(LIMB_DIR . '/class/core/tree/tree.class.php');
    $this->tree = new tree();
    
    return $this->tree;
  }
  
  public function getFetcher()
  {
    if($this->fetcher)
      return $this->fetcher;
    
    include_once(LIMB_DIR . '/class/core/fetcher.class.php');
    
    $this->fetcher = new fetcher();
    
    return $this->fetcher; 
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
}

?> 