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
require_once(LIMB_DIR . '/class/core/base_limb_toolkit.class.php');

class view_test_version{}

class base_limb_toolkit_test extends LimbTestCase
{
  var $toolkit;
  
  function setUp()
  {
    $this->toolkit = new BaseLimbToolkit();   
  }

  function test_define_constant()
  { 
    $const = md5(mt_rand());
    $this->toolkit->define($const, 'test-value');
    $this->assertEqual($this->toolkit->constant($const), 'test-value');
  }

  function test_create_db_table()
  { 
    $this->assertEqual(get_class($this->toolkit->createDBTable('sys_site_object')), 
                       'sys_site_object_db_table');
  }

  function test_get_datasource()
  { 
    $this->assertEqual(get_class($this->toolkit->getDatasource('site_objects_datasource')), 
                       'site_objects_datasource');
  }
  
  function test_create_site_object()
  {
    $this->assertEqual(get_class($this->toolkit->createSiteObject('site_object')), 
                       'site_object');    
  }

  function test_create_behaviour()
  {
    $this->assertEqual(get_class($this->toolkit->createBehaviour('site_object_behaviour')), 
                       'site_object_behaviour');    
  }

  function test_get_db()
  {
    $this->assertTrue(is_a($this->toolkit->getDB(), 'db_module'));    
  }

  function test_get_tree()
  {
    $this->assertEqual(get_class($this->toolkit->getTree()), 
                       'tree_decorator');    
  }
  
  function test_get_user()
  {
    $this->assertEqual(get_class($this->toolkit->getUser()), 'user');
  }
  
  function test_get_authenticator()
  { 
    $this->assertEqual(get_class($this->toolkit->getAuthenticator()), 
                       'simple_authenticator');    
  }
  
  function test_get_authorizer()
  { 
    $this->assertEqual(get_class($this->toolkit->getAuthorizer()), 
                       'simple_authorizer');    
  }

  function test_get_request()
  { 
    $this->assertEqual(get_class($this->toolkit->getRequest()), 
                       'request');    
  }

  function test_get_response()
  { 
    $this->assertEqual(get_class($this->toolkit->getResponse()), 
                       'http_response');    
  }

  function test_get_cache()
  { 
    $this->assertEqual(get_class($this->toolkit->getCache()), 
                       'CacheRegistry');    
  }

  function test_get_locale()
  { 
    $this->assertEqual(get_class($this->toolkit->getLocale()), 
                       'locale');    
  }

  function test_get_session()
  { 
    $this->assertEqual(get_class($this->toolkit->getSession()), 
                       'session');    
  }

  function test_get_dataspace()
  { 
    $this->assertEqual(get_class($this->toolkit->getDataspace()), 
                       'dataspace');    
  }

  function test_switch_dataspace()
  { 
    $d1 = $this->toolkit->getDataspace();
    $d2 = $this->toolkit->switchDataspace('test-dataspace');    

    $this->assertTrue($d1 !== $d2); 
    
    $d3 = $this->toolkit->switchDataspace('default');
    
    $this->assertTrue($d1 === $d3);
  }
  
  function test_set_get_view()
  { 
    $view = new view_test_version();
    $this->toolkit->setView($view);
    
    $this->assertEqual(get_class($this->toolkit->getView()), 
                       'view_test_version');    
  }
  
  
}

?> 
