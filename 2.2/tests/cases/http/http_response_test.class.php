<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/request/http_response.class.php');

Mock::generatePartial(
  'http_response',
  'special_mock_response',
  array('_pre_commit', '_post_commit', '_send_header', '_send_string')
);

class http_response_test extends UnitTestCase
{
  var $response;
  
  function setUp()
  {
    $this->response =& new special_mock_response($this);
  }
  
  function tearDown()
  {
    $this->response->tally();
  } 
    
  function test_header()
  {
    $this->response->expectArgumentsAt(0, '_send_header', array("Location:to-some-place"));
    $this->response->expectArgumentsAt(1, '_send_header', array("Location:to-some-place2"));
    $this->response->expectOnce('_pre_commit');
    $this->response->expectOnce('_post_commit');
    
    $this->response->header("Location:to-some-place");
    $this->response->header("Location:to-some-place2");
    $this->response->commit();
  }
  
  function test_is_empty()
  {
    $this->assertTrue($this->response->is_empty());
  }

  function test_not_empty_redirect()
  {
    $this->response->redirect("/to/some/place?t=1&amp;t=2");
    $this->assertFalse($this->response->is_empty());
  }

  function test_not_empty_response_string()
  {
    $this->response->write("<b>wow</b>");
    $this->assertFalse($this->response->is_empty());
  }
  
  function test_headers_not_sent()
  {
    $this->assertFalse($this->response->headers_sent());
  }
  
  function test_headers_sent()
  {
    $this->response->header("Location:to-some-place");
    $this->assertTrue($this->response->headers_sent());
  }  
  
  function test_redirect()
  {
    $this->response->expectOnce('_send_string', array("<html><head><meta http-equiv=refresh content='0;url=/to/some/place?t=1&t=2'></head><body bgcolor=white></body></html>"));
    $this->response->expectOnce('_pre_commit');
    $this->response->expectOnce('_post_commit');   
    
    $this->response->redirect("/to/some/place?t=1&t=2");
    $this->response->commit();
  }
    
  function test_write()
  {
    $this->response->expectOnce('_send_string', array("<b>wow</b>"));
    $this->response->expectOnce('_pre_commit');
    $this->response->expectOnce('_post_commit');   
    
    $this->response->write("<b>wow</b>");
    $this->response->commit();
  }
}

?>