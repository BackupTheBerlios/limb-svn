<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: authentication_filter_test.class.php 814 2004-10-21 12:46:23Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/filters/filter_chain.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object_controller.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/datasources/requested_object_datasource.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');
require_once(LIMB_DIR . '/class/core/request/http_response.class.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');
require_once(LIMB_DIR . '/class/core/permissions/authenticator.interface.php');
require_once(LIMB_DIR . '/class/core/permissions/authorizer.interface.php');

require_once(dirname(__FILE__) . '/../../../filters/simple_permissions_authentication_filter.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('filter_chain');
Mock :: generate('http_response');
Mock :: generate('requested_object_datasource');
Mock :: generate('request');
Mock :: generate('site_object_controller');
Mock :: generate('site_object_behaviour');
Mock :: generate('response');
Mock :: generate('user');
Mock :: generate('authenticator');
Mock :: generate('authorizer');

Mock :: generatePartial('simple_permissions_authentication_filter',
                        'simple_permissions_authentication_filter_test_version',
                        array('_get_controller',
                              'get_behaviour_by_object_id',
                              'initialize_user',
                              'process_404_error')); 

class simple_permissions_authentication_filter_test extends LimbTestCase
{
  var $filter_chain;
  var $filter;
  var $request;
  var $datasource;
  var $toolkit;
  var $response;
  
  function setUp()
  {
    $this->filter = new simple_permissions_authentication_filter_test_version($this);
    
    $this->toolkit = new MockLimbToolkit($this);
    $this->datasource = new Mockrequested_object_datasource($this);
    $this->request = new Mockrequest($this);
    $this->filter_chain = new Mockfilter_chain($this);
    $this->response = new Mockhttp_response($this);
    
    Limb :: registerToolkit($this->toolkit);
  }
  
  function tearDown()
  {
    $this->request->tally();
    $this->response->tally();  

    $this->toolkit->tally();

    Limb :: popToolkit();    
  }
 
  function test_run_node_not_found()
  {
    $this->toolkit->setReturnValue('getDatasource', 
                                   $this->datasource, 
                                   array('requested_object_datasource'));
    
    $this->datasource->setReturnValue('map_request_to_node', 
                                      null, 
                                      array(new IsAExpectation('Mockrequest')));
    
    $this->filter->expectOnce('process_404_error');
    $this->filter_chain->expectOnce('next');
    
    $this->filter->run($this->filter_chain, $this->request, $this->response);
    
    $this->filter->tally();
    $this->filter_chain->tally();
  }

  function test_run_no_such_action()
  {
    $this->toolkit->setReturnValue('getDatasource', 
                                   $this->datasource, 
                                   array('requested_object_datasource'));
    
    $this->datasource->setReturnValue('map_request_to_node', 
                                      array('object_id' => $object_id = 100), 
                                      array(new IsAExpectation('Mockrequest')));
    
    $controller = new Mocksite_object_controller($this);
    $behaviour = new Mocksite_object_behaviour($this);
    
    $this->filter->setReturnValue('get_behaviour_by_object_id', $behaviour, array($object_id));
    
    $this->filter->setReturnValue('_get_controller', 
                                  $controller, 
                                  array(new IsAExpectation('Mocksite_object_behaviour')));
    
    $controller->setReturnValue('get_requested_action', null);
    
    $this->filter->expectOnce('process_404_error');
    $this->filter_chain->expectOnce('next');
    
    $this->filter->run($this->filter_chain, $this->request, $this->response);
    
    $this->filter->tally();
    $this->filter_chain->tally();
  }

  function test_run_object_is_not_accessible()
  {
    $this->toolkit->setReturnValue('getDatasource', 
                                   $this->datasource, 
                                   array('requested_object_datasource'));
    
    $this->datasource->setReturnValue('map_request_to_node', 
                                      array('object_id' => $object_id = 100), 
                                      array(new IsAExpectation('Mockrequest')));
    
    $controller = new Mocksite_object_controller($this);
    $behaviour = new Mocksite_object_behaviour($this);
    
    $this->filter->setReturnValue('get_behaviour_by_object_id', $behaviour, array($object_id));
    
    $this->filter->setReturnValue('_get_controller', 
                                  $controller, 
                                  array(new IsAExpectation('Mocksite_object_behaviour')));
    
    $controller->setReturnValue('get_requested_action', $action = 'some_actin');

    $this->datasource->expectOnce('set_request', array(new IsAExpectation('Mockrequest'))); 

    $this->datasource->setReturnValue('fetch', null); 

    $this->response->expectOnce('redirect'); 
    
    $this->filter_chain->expectNever('next');
    
    $this->filter->run($this->filter_chain, $this->request, $this->response);
    
    $this->filter->tally();
    $this->filter_chain->tally();
  }

  function test_run_action_is_not_accessible()
  {
    $this->toolkit->setReturnValue('getDatasource', 
                                   $this->datasource, 
                                   array('requested_object_datasource'));
    
    $this->datasource->setReturnValue('map_request_to_node', 
                                      array('object_id' => $object_id = 100), 
                                      array(new IsAExpectation('Mockrequest')));
    
    $controller = new Mocksite_object_controller($this);
    $behaviour = new Mocksite_object_behaviour($this);
    
    $this->filter->setReturnValue('get_behaviour_by_object_id', $behaviour, array($object_id));
    
    $this->filter->setReturnValue('_get_controller', 
                                  $controller, 
                                  array(new IsAExpectation('Mocksite_object_behaviour')));
    
    $controller->setReturnValue('get_requested_action', $action = 'some_actin');

    $this->datasource->expectOnce('set_request', array(new IsAExpectation('Mockrequest'))); 

    $object_data = array('actions' => array(), 'behaviour' => 'some_behaviour');
    $this->datasource->setReturnValue('fetch', $object_data); 

    $authorizer = new Mockauthorizer($this);
    $authorizer->expectOnce('assign_actions_to_objects', array($object_data));

    $this->toolkit->setReturnValue('getAuthorizer', $authorizer);
    
    $this->response->expectOnce('redirect'); 
    
    $this->filter_chain->expectNever('next');
    
    $this->filter->run($this->filter_chain, $this->request, $this->response);
    
    $this->filter->tally();
    $this->filter_chain->tally();
  }
  
  function test_run_ok()
  {
    $this->toolkit->setReturnValue('getDatasource', 
                                   $this->datasource, 
                                   array('requested_object_datasource'));
    
    $this->datasource->setReturnValue('map_request_to_node', 
                                      array('object_id' => $object_id = 100), 
                                      array(new IsAExpectation('Mockrequest')));
    
    $controller = new Mocksite_object_controller($this);
    $behaviour = new Mocksite_object_behaviour($this);
    
    $this->filter->setReturnValue('get_behaviour_by_object_id', $behaviour, array($object_id));
    
    $this->filter->setReturnValue('_get_controller', 
                                  $controller, 
                                  array(new IsAExpectation('Mocksite_object_behaviour')));
    
    $controller->setReturnValue('get_requested_action', $action = 'some_action');

    $this->datasource->expectOnce('set_request', array(new IsAExpectation('Mockrequest'))); 

    $object_data = array('actions' => array('some_action' => array()), 'behaviour' => 'some_behaviour');
    $this->datasource->setReturnValue('fetch', $object_data); 

    $authorizer = new Mockauthorizer($this);
    $authorizer->expectOnce('assign_actions_to_objects', array($object_data));

    $this->toolkit->setReturnValue('getAuthorizer', $authorizer);

    $this->response->expectNever('redirect'); 
    
    $this->filter_chain->expectOnce('next');
    
    $this->filter->run($this->filter_chain, $this->request, $this->response);
    
    $this->filter->tally();
    $this->filter_chain->tally();
  }
}

?>