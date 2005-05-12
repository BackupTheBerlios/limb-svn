<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/request/http_redirect_strategy.class.php');
require_once(LIMB_DIR . '/core/request/http_response.class.php');

Mock :: generate('http_response');

class http_redirect_strategy_test extends LimbTestCase
{
  var $response;

  function setUp()
  {
    $this->response =& new Mockhttp_response($this);
  }

  function tearDown()
  {
    $this->response->tally();
  }

  function test_redirect()
  {
    $strategy =& new http_redirect_strategy();

    $path = '/to/some/place';

    $this->response->expectOnce('header', array("Location: {$path}"));

    $strategy->redirect($this->response, $path);
  }
}

?>