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
require_once(WACT_ROOT . '/template/template.inc.php');

class LimbRequestTransferTagTestCase extends LimbTestCase
{
  var $request;

  function LimbRequestTransferTagTestCase()
  {
    $toolkit =& Limb :: toolkit();

    $this->request =& $toolkit->getRequest();

    parent :: LimbTestCase('limb request trasfer tag case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testTransferNoSuchAttributesInRequest()
  {
    $this->request->set('p1', 'test1');

    $template = '<limb:REQUEST_TRANSFER attributes="p2,p3">' .
                '<form action="/some/path">' .
                '</form>' .
                '</limb:REQUEST_TRANSFER>';

    RegisterTestingTemplate('/limb/request_transfer_no_attrs.html', $template);

    $page =& new Template('/limb/request_transfer_no_attrs.html');
    $this->assertEqual($page->capture(), '<form action="/some/path"></form>');

    $this->request->remove('p1');
  }

  function testTransferCheckQuotes()
  {
    $this->request->set('p1', 'test1');

    $template = '<limb:REQUEST_TRANSFER attributes="p1">' .
                '<form action=/some/path>' .
                '<area src=\'http://test/root\'>' .
                '<a href="/test"></a>' .
                '</area><frame src=whatever></form>' .
                '</limb:REQUEST_TRANSFER>';

    RegisterTestingTemplate('/limb/request_transfer_quotes.html', $template);

    $page =& new Template('/limb/request_transfer_quotes.html');

    //somehow WACT sets quotes automatically...
    $expected = '<form action=/some/path?&p1=test1>' .
                '<area src="http://test/root?&p1=test1">' .
                '<a href="/test?&p1=test1"></a>' .
                '</area><frame src="whatever?&p1=test1"></form>';

    $this->assertEqual($page->capture(), $expected);

    $this->request->remove('p1');
  }

  function testTransferAddSlashes()
  {
    $this->request->set('p1', 'test"test');

    $template = '<limb:REQUEST_TRANSFER attributes="p1">' .
                '<form action="/some/path"></form>' .
                '</limb:REQUEST_TRANSFER>';

    RegisterTestingTemplate('/limb/request_transfer_slash.html', $template);

    $page =& new Template('/limb/request_transfer_slash.html');
    $this->assertEqual($page->capture(), '<form action="/some/path?&p1=test\"test"></form>');

    $this->request->remove('p1');
  }

  function testTransferForm()
  {
    $this->request->set('p1', 'test1');
    $this->request->set('p2', 'test2');

    $template = '<limb:REQUEST_TRANSFER attributes="p1,p2">' .
                '<form action="/some/path"></form>' .
                '</limb:REQUEST_TRANSFER>';

    RegisterTestingTemplate('/limb/request_transfer_form.html', $template);

    $page =& new Template('/limb/request_transfer_form.html');
    $this->assertEqual($page->capture(), '<form action="/some/path?&p1=test1&p2=test2"></form>');

    $this->request->remove('p1');
    $this->request->remove('p2');
  }

  function testTransferHref()
  {
    $this->request->set('p1', 'test1');
    $this->request->set('p2', 'test2');

    $template = '<limb:REQUEST_TRANSFER attributes="p1,p2">' .
                '<a href="/some/path">content</a>' .
                '</limb:REQUEST_TRANSFER>';

    RegisterTestingTemplate('/limb/request_transfer_a.html', $template);

    $page =& new Template('/limb/request_transfer_a.html');
    $this->assertEqual($page->capture(), '<a href="/some/path?&p1=test1&p2=test2">content</a>');

    $this->request->remove('p1');
    $this->request->remove('p2');
  }

  function testTransferArea()
  {
    $this->request->set('p1', 'test1');
    $this->request->set('p2', 'test2');

    $template = '<limb:REQUEST_TRANSFER attributes="p1,p2">' .
                '<area src="/some/path">content\ncontent2</area>' .
                '</limb:REQUEST_TRANSFER>';

    RegisterTestingTemplate('/limb/request_transfer_area.html', $template);

    $page =& new Template('/limb/request_transfer_area.html');
    $this->assertEqual($page->capture(), '<area src="/some/path?&p1=test1&p2=test2">content\ncontent2</area>');

    $this->request->remove('p1');
    $this->request->remove('p2');
  }

  function testTransferFrame()
  {
    $this->request->set('p1', 'test1');
    $this->request->set('p2', 'test2');

    $template = '<limb:REQUEST_TRANSFER attributes="p1,p2">' .
                '<frame src="/some/path"/>' .
                '</limb:REQUEST_TRANSFER>';

    RegisterTestingTemplate('/limb/request_transfer_frame.html', $template);

    $page =& new Template('/limb/request_transfer_frame.html');
    $this->assertEqual($page->capture(), '<frame src="/some/path?&p1=test1&p2=test2" />');

    $this->request->remove('p1');
    $this->request->remove('p2');
  }
}
?>
