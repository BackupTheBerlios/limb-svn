<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbListTagTest.class.php 1209 2005-04-08 14:29:41Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');

class LimbSelectOptionsSourceTagTestCase extends LimbTestCase
{
  function LimbSelectOptionsSourceTagTestCase()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testTargetNotFound()
  {
    $template = '<core:DATASOURCE id="data">' .
                '<limb:select_options_source target="select" from="source">' .
                '</core:DATASOURCE>';

    RegisterTestingTemplate('/limb/select_options_error.html', $template);

    $page =& new Template('/limb/select_options_error.html');

    $this->assertErrorPattern('~COMPONENTNOTFOUND~');
    $this->swallowErrors();
  }

  function testTargetIsNotSupported()
  {
    $template = '<core:DATASOURCE id="data">' .
                '<limb:select_options_source target="select" from="source">' .
                '<core:DATASOURCE id="select"></core:DATASOURCE>' .
                '</core:DATASOURCE>';

    RegisterTestingTemplate('/limb/select_options_not_supported.html', $template);

    $page =& new Template('/limb/select_options_not_supported.html');

    $this->assertErrorPattern('~MISSINGCHILDTAG~');
    $this->swallowErrors();
  }

  function testTakeOptionsFrom()
  {
    $template = '<core:DATASOURCE id="data">' .
                '<limb:select_options_source target="select" from="source">' .
                '<form runat="server">' .
                '<select id="select" name="select"></select>' .
                '</form>' .
                '</core:DATASOURCE>';

    $options = array('4' => 'red', '5' => 'blue');

    RegisterTestingTemplate('/limb/select_options_source.html', $template);

    $page =& new Template('/limb/select_options_source.html');

    $data =& $page->getChild('data');
    $data->set('source', $options);

    $this->assertEqual($page->capture(),
                       '<form>'.
                       '<select id="select" name="select"><option value="4">red</option><option value="5">blue</option></select>'.
                       '</form>');
  }

  function testOptionsViaRegisterDataSet()
  {
    $template = '<limb:select_options_source id="source" target="select">' .
                '<form runat="server">' .
                '<select id="select" name="select"></select>' .
                '</form>';

    $options = array(array('4' => 'red'), array('5' => 'blue'));

    RegisterTestingTemplate('/limb/select_options_source2.html', $template);

    $page =& new Template('/limb/select_options_source2.html');

    $data =& $page->getChild('source');

    $data->registerDataSet(new ArrayDataSet($options));

    $this->assertEqual($page->capture(),
                       '<form>'.
                       '<select id="select" name="select"><option value="4">red</option><option value="5">blue</option></select>'.
                       '</form>');
  }
}
?>

