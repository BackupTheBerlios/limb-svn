<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbFormTagTest.class.php 1013 2005-01-12 12:13:22Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(WACT_ROOT . '/template/components/form/form.inc.php');

class LimbPreserveStateTagTestCase extends LimbTestCase
{
  function LimbPreserveStateTagTestCase()
  {
    parent :: LimbTestCase('limb preserve state tag case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testPreserveState()
  {
    $template = '<form id="testForm" name="testForm" runat="server">' .
                '<limb:PRESERVE_STATE name="id">' .
                '</form>';

    RegisterTestingTemplate('/limb/preserve_state1.html', $template);

    $data = new Dataspace();
    $data->set('id', 10000);

    $page =& new Template('/limb/preserve_state1.html');
    $form =& $page->findChild('testForm');
    $form->registerDatasource($data);

    $this->assertEqual($page->capture(),
                       '<form id="testForm" name="testForm">' .
                       '<input type="hidden" name="id" value="10000">' .
                       '</form>');
  }
}
?>
