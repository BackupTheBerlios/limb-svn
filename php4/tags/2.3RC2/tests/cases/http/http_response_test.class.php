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
require_once(LIMB_DIR . '/core/request/http_response.class.php');

Mock::generatePartial(
  'http_response',
  'special_mock_response',
  array('_send_header', '_send_string', '_send_file', '_exit')
);

class http_response_test extends LimbTestCase
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

    $this->response->header("Location:to-some-place");
    $this->response->header("Location:to-some-place2");
    $this->response->commit();
  }

  function test_is_empty()
  {
    $this->assertTrue($this->response->is_empty());
  }

  function test_is_empty_headers_sent()
  {
    $this->response->header('test');
    $this->assertTrue($this->response->is_empty());
  }

  function test_not_empty_response_string()
  {
    $this->response->write("<b>wow</b>");
    $this->assertFalse($this->response->is_empty());
  }

  function test_not_empty_readfile()
  {
    $this->response->readfile("/path/to/file");
    $this->assertFalse($this->response->is_empty());
  }

  function test_not_empty_304_status()
  {
    $this->response->header('HTTP/1.0 304 Not Modified');
    $this->assertFalse($this->response->is_empty());
  }

  function test_not_empty_412_status()
  {
    $this->response->header('HTTP/1.1 412 Precondition Failed');
    $this->assertFalse($this->response->is_empty());
  }

  function test_headers_not_sent()
  {
    $this->assertFalse($this->response->headers_sent());
  }

  function test_file_not_sent()
  {
    $this->assertFalse($this->response->file_sent());
  }

  function test_file_sent()
  {
    $this->response->readfile('somefile');
    $this->assertTrue($this->response->file_sent());
  }

  function test_headers_sent()
  {
    $this->response->header("Location:to-some-place");
    $this->assertTrue($this->response->headers_sent());
  }

  function test_exit_after_commit()
  {
    $this->response->expectOnce('_exit');
    $this->response->commit();
  }

  function test_redirect()
  {
    $this->assertFalse($this->response->is_redirected());

    $this->response->redirect('some path');

    $this->assertTrue($this->response->is_redirected());
  }

  function test_write()
  {
    $this->response->expectOnce('_send_string', array("<b>wow</b>"));

    $this->response->write("<b>wow</b>");
    $this->response->commit();
  }

  function test_readfile()
  {
    $this->response->expectOnce('_send_file', array("/path/to/file"));

    $this->response->readfile("/path/to/file");
    $this->response->commit();
  }

  function test_get_response_default_status()
  {
    $this->assertEqual($this->response->get_status(), 200);
  }

  function test_get_response_status_http()
  {
    $this->response->header('HTTP/1.0  304 ');
    $this->assertEqual($this->response->get_status(), 304);

    $this->response->header('HTTP/1.1  412');
    $this->assertEqual($this->response->get_status(), 412);
  }

  function test_get_unknown_directive()
  {
    $this->assertFalse($this->response->get_directive('cache-control'));
  }

  function test_get_directive()
  {
    $this->response->header('Cache-Control: private, max-age=0, must-revalidate');
    $this->assertEqual($this->response->get_directive('cache-control'), 'private, max-age=0, must-revalidate');

    $this->response->header('Cache-Control :    private, max-age=10  ');
    $this->assertEqual($this->response->get_directive('cache-control'), 'private, max-age=10');
  }

  function test_get_content_default_type()
  {
    $this->assertEqual($this->response->get_content_type(), 'text/html');
  }

  function test_get_content_type()
  {
    $this->response->header('Content-Type: image/png');
    $this->assertEqual($this->response->get_content_type(), 'image/png');

    $this->response->header('Content-Type: application/rss+xml');
    $this->assertEqual($this->response->get_content_type(), 'application/rss+xml');
  }

}

?>