<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbFormTagTest.class.php 1209 2005-04-08 14:29:41Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(WACT_ROOT . '/template/components/form/form.inc.php');

class LimbFormRefererTagTestCase extends LimbTestCase
{
  var $old_server;

  function LimbFormRefererTagTestCase()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    if(isset($_SERVER))
      $this->old_server = $_SERVER;
  }

  function tearDown()
  {
    ClearTestingTemplates();

    if($this->old_server)
      $_SERVER = $this->old_server;
  }

  function testRefererFormNotSubmitted()
  {
    $template = "<form name='test' runat='server'><limb:form:REFERER></form>";

    RegisterTestingTemplate('/limb/form-ref1.html', $template);

    $page =& new Template('/limb/form-ref1.html');

    $referer = 'put-me-into-result';
    $_SERVER['HTTP_REFERER'] = $referer;

    $result = $page->capture();
    $this->assertEqual($result,
                       "<form name=\"test\"><input type='hidden' name='referer' value='$referer'></form>");
  }

  function testRefererFormNotSubmittedNoReferer()
  {
    $template = "<form name='test' runat='server'><limb:form:REFERER></form>";

    RegisterTestingTemplate('/limb/form-ref2.html', $template);

    $page =& new Template('/limb/form-ref2.html');

    $_SERVER['HTTP_REFERER'] = null;

    $result = $page->capture();
    $this->assertEqual($result,
                       "<form name=\"test\"></form>");
  }

  function testRefererFormSubmitted()
  {
    $template = "<form id='test' runat='server'><limb:form:REFERER></form>";

    RegisterTestingTemplate('/limb/form-ref3.html', $template);

    $page =& new Template('/limb/form-ref3.html');

    $referer = 'put-me-into-result';
    $dataspace = new Dataspace();
    $dataspace->set('referer', $referer);

    $form =& $page->getChild('test');
    $form->registerDataSource($dataspace);

    $_SERVER['HTTP_REFERER'] = 'another-referer';

    $result = $page->capture();
    $this->assertEqual($result,
                       "<form id=\"test\"><input type='hidden' name='referer' value='$referer'></form>");
  }

  function testRefererFormNotSubmittedUseCurrent()
  {
    $template = "<form name='test' runat='server'><limb:form:REFERER use_current='TRUE'></form>";

    RegisterTestingTemplate('/limb/form-ref4.html', $template);

    $page =& new Template('/limb/form-ref4.html');

    $referer = 'put-me-into-result';
    $_SERVER['HTTP_REFERER'] = 'another-referer';
    $_SERVER['REQUEST_URI'] = $referer;

    $result = $page->capture();
    $this->assertEqual($result,
                       "<form name=\"test\"><input type='hidden' name='referer' value='$referer'></form>");
  }

  function testRefererFormSubmittedUseCurrent()
  {
    $template = "<form id='test' runat='server'><limb:form:REFERER use_current='TRUE'></form>";

    RegisterTestingTemplate('/limb/form-ref5.html', $template);

    $page =& new Template('/limb/form-ref5.html');

    $referer = 'put-me-into-result';
    $dataspace = new Dataspace();
    $dataspace->set('referer', $referer);

    $form =& $page->getChild('test');
    $form->registerDataSource($dataspace);

    $_SERVER['HTTP_REFERER'] = 'another-referer';
    $_SERVER['REQUEST_URI'] = 'another-referer';

    $result = $page->capture();
    $this->assertEqual($result,
                       "<form id=\"test\"><input type='hidden' name='referer' value='$referer'></form>");
  }

}
?>
