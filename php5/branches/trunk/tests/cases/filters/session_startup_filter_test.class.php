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
require_once(LIMB_DIR . '/class/core/filters/session_startup_filter.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/session/session.class.php');
require_once(LIMB_DIR . '/class/core/datasources/requested_object_datasource.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/request/http_response.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('filter_chain');
Mock :: generate('http_response');
Mock :: generate('session');
Mock :: generate('request');
Mock :: generate('authenticator');

class session_startup_filter_test extends LimbTestCase
{
  var $filter_chain;
  var $filter;
  var $request;
  var $toolkit;
  var $response;
  var $session;
  
  function setUp()
  {
    $this->filter = new session_startup_filter($this);
    
    $this->toolkit = new MockLimbToolkit($this);    
    $this->request = new Mockrequest($this);
    $this->filter_chain = new Mockfilter_chain($this);
    $this->response = new Mockhttp_response($this);
    $this->session = new Mocksession($this);
    
    $this->toolkit->setReturnValue('getSession', $this->session);
    
    Limb :: registerToolkit($this->toolkit);
  }
  
  function tearDown()
  {
    $this->request->tally();
    $this->response->tally();  
    $this->session->tally();
    $this->toolkit->tally();
    $this->filter_chain->tally();

    Limb :: popToolkit();    
  }
    
  function test_run()
  {  
    $this->session->expectOnce('start');
    $this->filter_chain->expectOnce('next');
    
    $this->filter->run($this->filter_chain, $this->request, $this->response);    
  }
}

?>