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
require_once(LIMB_DIR . '/core/request/meta_redirect_strategy.class.php');
require_once(LIMB_DIR . '/core/request/http_response.class.php');

Mock :: generate('http_response');

class meta_redirect_strategy_test extends LimbTestCase
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

  function test_default_redirect()
  {
    $strategy =& new meta_redirect_strategy();

    $path = '/to/some/place?t=1&t=2';
    $message = strings :: get('redirect_message');
    $message = str_replace('%path%', $path, $message);

    $this->response->expectOnce('write',
                                array(new WantedPatternExpectation("~^<html><head><meta http-equiv=refresh content='0;" .
                                                                   "url=" . preg_quote($path) . "'~")));

    $strategy->redirect($this->response, $path);
  }

  //we depend upon /redirect_template.html, make it possible to test it
  //using on-the-fly template!!!
  function test_template_redirect()
  {
    $strategy =& new meta_redirect_strategy('/redirect_template.html');

    $path = '/to/some/place?t=1&t=2';
    $message = strings :: get('redirect_message');
    $message = str_replace('%path%', $path, $message);

    $this->response->expectOnce('write',
                                array(new WantedPatternExpectation("~^<html><head><meta http-equiv=refresh content='0;" .
                                                                   "url=" . preg_quote($path) . "'></head>" .
                                                                   "<body bgcolor=white><font color=707070>~")));

    $strategy->redirect($this->response, $path);
  }
}

?>