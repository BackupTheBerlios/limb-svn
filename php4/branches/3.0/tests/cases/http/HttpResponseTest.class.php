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
require_once(LIMB_DIR . '/core/request/HttpResponse.class.php');
require_once(LIMB_DIR . '/core/i18n/Strings.class.php');
require_once(LIMB_DIR . '/core/request/HttpRedirectStrategy.class.php');

Mock :: generatePartial(
  'HttpResponse',
  'SpecialMockResponse',
  array('_sendHeader', '_sendString', '_sendFile', '_exit')
);

Mock::generate('HttpRedirectStrategy');

class HttpResponseTest extends LimbTestCase
{
  var $response;

  function HttpResponseTest()
  {
    parent :: LimbTestCase('http response test');
  }

  function setUp()
  {
    $this->response = new SpecialMockResponse($this);
  }

  function tearDown()
  {
    $this->response->tally();
  }

  function testHeader()
  {
    $this->response->expectArgumentsAt(0, '_sendHeader', array("Location:to-some-place"));
    $this->response->expectArgumentsAt(1, '_sendHeader', array("Location:to-some-place2"));

    $this->response->header("Location:to-some-place");
    $this->response->header("Location:to-some-place2");
    $this->response->commit();
  }

  function testIsEmpty()
  {
    $this->assertTrue($this->response->isEmpty());
  }

  function testIsEmptyHeadersSent()
  {
    $this->response->header('test');
    $this->assertTrue($this->response->isEmpty());
  }

  function testNotEmptyRedirect()
  {
    $this->response->redirect("/to/some/place?t=1&amp;t=2");
    $this->assertFalse($this->response->isEmpty());
  }

  function testNotEmptyResponseString()
  {
    $this->response->write("<b>wow</b>");
    $this->assertFalse($this->response->isEmpty());
  }

  function testNotEmptyReadfile()
  {
    $this->response->readfile("/path/to/file");
    $this->assertFalse($this->response->isEmpty());
  }

  function testNotEmpty304Status()
  {
    $this->response->header('HTTP/1.0 304 Not Modified');
    $this->assertFalse($this->response->isEmpty());
  }

  function testNotEmpty412Status()
  {
    $this->response->header('HTTP/1.1 412 Precondition Failed');
    $this->assertFalse($this->response->isEmpty());
  }

  function testHeadersNotSent()
  {
    $this->assertFalse($this->response->headers_sent());
  }

  function testFileNotSent()
  {
    $this->assertFalse($this->response->fileSent());
  }

  function testFileSent()
  {
    $this->response->readfile('somefile');
    $this->assertTrue($this->response->fileSent());
  }

  function testHeadersSent()
  {
    $this->response->header("Location:to-some-place");
    $this->assertTrue($this->response->headers_sent());
  }

  function testExitAfterCommit()
  {
    $this->response->expectOnce('_exit');
    $this->response->commit();
  }

  function testRedirect()
  {
    $this->assertFalse($this->response->isRedirected());

    $this->response->redirect('some path');

    $this->assertTrue($this->response->isRedirected());
  }

  function testRedirectOnlyOnce()
  {
    $strategy =& new MockHttpRedirectStrategy($this);

    $this->response->setRedirectStrategy($strategy);

    $this->assertFalse($this->response->isRedirected());

    $strategy->expectOnce('redirect');
    $this->response->redirect('some path');
    $this->response->redirect('some other path');

    $this->assertTrue($this->response->isRedirected());

    $this->response->commit();
  }

  function testWrite()
  {
    $this->response->expectOnce('_sendString', array("<b>wow</b>"));

    $this->response->write("<b>wow</b>");
    $this->response->commit();
  }

  function testReadfile()
  {
    $this->response->expectOnce('_sendFile', array("/path/to/file"));

    $this->response->readfile("/path/to/file");
    $this->response->commit();
  }

  function testGetResponseDefaultStatus()
  {
    $this->assertEqual($this->response->getStatus(), 200);
  }

  function testGetResponseStatusHttp()
  {
    $this->response->header('HTTP/1.0  304 ');
    $this->assertEqual($this->response->getStatus(), 304);

    $this->response->header('HTTP/1.1  412');
    $this->assertEqual($this->response->getStatus(), 412);
  }

  function testGetUnknownDirective()
  {
    $this->assertFalse($this->response->getDirective('cache-control'));
  }

  function testGetDirective()
  {
    $this->response->header('Cache-Control: protected, max-age=0, must-revalidate');
    $this->assertEqual($this->response->getDirective('cache-control'), 'protected, max-age=0, must-revalidate');

    $this->response->header('Cache-Control :    protected, max-age=10  ');
    $this->assertEqual($this->response->getDirective('cache-control'), 'protected, max-age=10');
  }

  function testGetContentDefaultType()
  {
    $this->assertEqual($this->response->getContentType(), 'text/html');
  }

  function testGetContentType()
  {
    $this->response->header('Content-Type: image/png');
    $this->assertEqual($this->response->getContentType(), 'image/png');

    $this->response->header('Content-Type: application/rss+xml');
    $this->assertEqual($this->response->getContentType(), 'application/rss+xml');
  }

}

?>