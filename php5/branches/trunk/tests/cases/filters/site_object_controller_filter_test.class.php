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
require_once(LIMB_DIR . '/class/core/filters/filter_chain.class.php');
require_once(LIMB_DIR . '/class/core/filters/site_object_controller_filter.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/datasources/requested_object_datasource.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');
require_once(LIMB_DIR . '/class/core/request/response.interface.php');

Mock :: generate('LimbToolkit');
Mock :: generate('filter_chain');
Mock :: generate('request');
Mock :: generate('requested_object_datasource');
Mock :: generate('site_object');
Mock :: generate('site_object_controller');
Mock :: generate('site_object_behaviour');
Mock :: generate('response');

Mock :: generatePartial('site_object_controller_filter',
                        'site_object_controller_filter_test_version',
                        array('_get_controller')); 

class site_object_controller_filter_test extends LimbTestCase
{
  var $filter_chain;
  var $filter;
  var $request;
  var $datasource;
  var $toolkit;
  var $site_object;
  var $controller;
  var $behaviour;
  var $response;
  
  function setUp()
  {
    $this->filter = new site_object_controller_filter_test_version($this);
    
    $this->toolkit = new MockLimbToolkit($this);
    $this->site_object = new Mocksite_object($this);
    $this->request = new Mockrequest($this);
    $this->filter_chain = new Mockfilter_chain($this);
    $this->datasource = new Mockrequested_object_datasource($this);
    $this->controller = new Mocksite_object_controller($this);
    $this->behaviour = new Mocksite_object_behaviour($this);
    $this->response = new Mockresponse($this);
    
    $this->datasource->expectOnce('set_request', array(new IsAExpectation('Mockrequest')));
    $this->datasource->expectOnce('fetch');
    
    $this->toolkit->setReturnValue('getDatasource', 
                                   $this->datasource, 
                                   array('requested_object_datasource'));
    
    $this->toolkit->setReturnValue('createSiteObject', $this->site_object, array('site_object'));
    
    $this->filter_chain->expectOnce('next');
    
    $this->filter->setReturnValue('_get_controller', $this->controller);
    
    $this->site_object->setReturnValue('get_behaviour', $this->behaviour);
    $this->controller->expectOnce('process', array(new IsAExpectation('Mocksite_object_behaviour')));
    
    Limb :: registerToolkit($this->toolkit);
  }
  
  function tearDown()
  {
    $this->request->tally();
    $this->filter_chain->tally();
    $this->datasource->tally();
    $this->site_object->tally();
    $this->filter->tally();
    $this->behaviour->tally();
    $this->response->tally();  

    Limb :: popToolkit();    
  }
  
  function test_run()
  {
    $this->datasource->setReturnValue('fetch', array('class_name' => 'site_object'));
    
    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }
}

?>