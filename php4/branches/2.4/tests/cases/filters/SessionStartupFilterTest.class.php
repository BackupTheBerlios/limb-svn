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
require_once(LIMB_DIR . '/class/filters/FilterChain.class.php');
require_once(LIMB_DIR . '/class/filters/SessionStartupFilter.class.php');
require_once(LIMB_DIR . '/class/request/Request.class.php');
require_once(LIMB_DIR . '/class/session/Session.class.php');
require_once(LIMB_DIR . '/class/datasources/RequestedObjectDatasource.class.php');
require_once(LIMB_DIR . '/class/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/request/HttpResponse.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('FilterChain');
Mock :: generate('HttpResponse');
Mock :: generate('Session');
Mock :: generate('Request');
Mock :: generate('Authenticator');

class SessionStartupFilterTest extends LimbTestCase
{
  var $filter_chain;
  var $filter;
  var $request;
  var $toolkit;
  var $response;
  var $session;

  function setUp()
  {
    $this->filter = new SessionStartupFilter($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->request = new MockRequest($this);
    $this->filter_chain = new MockFilterChain($this);
    $this->response = new MockHttpResponse($this);
    $this->session = new MockSession($this);

    $this->toolkit->setReturnReference('getSession', $this->session);

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

  function testRun()
  {
    $this->session->expectOnce('start');
    $this->filter_chain->expectOnce('next');

    $this->filter->run($this->filter_chain, $this->request, $this->response);
  }
}

?>