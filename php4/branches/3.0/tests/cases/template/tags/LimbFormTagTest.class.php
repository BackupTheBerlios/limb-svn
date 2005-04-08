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
require_once(WACT_ROOT . '/template/components/form/form.inc.php');

class LimbFormTagTestCase extends LimbTestCase
{
  function LimbFormTagTestCase()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testChildFormElementsNamesWrapping()
  {
    $template = '<limb:FORM id="testForm" name="testForm">
                    <label id="testLabel" name="label" for="testId">A label</label>
                    <input id="testInput" name="testInput" type="text">
                </limb:FORM>';

    RegisterTestingTemplate('/limb/form.html', $template);

    $page =& new Template('/limb/form.html');

    $input =& $page->getChild('testInput');
    $label =& $page->getChild('testLabel');

    $this->assertEqual($input->getAttribute('name'), 'testForm[testInput]');
    $this->assertEqual($label->getAttribute('name'), 'label');
  }

  function testChildFormElementsNamesWrappingFilter()
  {
    $template = '<limb:FORM id="testForm" name="testForm">
                    <input id="testInput" name="{$\'testInput\'|uppercase}" type="text">
                </limb:FORM>';

    RegisterTestingTemplate('/limb/form2.html', $template);

    $page =& new Template('/limb/form2.html');

    $input =& $page->getChild('testInput');

    $this->assertEqual($input->getAttribute('name'), 'testForm[TESTINPUT]');
  }

  function testChildFormElementsNamesWrappingDBE()
  {
    $template = '<core:SET name="testinput">
                <limb:FORM id="testForm" name="testForm">
                    <input id="testInput" name="{$^name|uppercase}" type="text">
                </limb:FORM>';

    RegisterTestingTemplate('/limb/form3.html', $template);

    $page =& new Template('/limb/form3.html');

    $input =& $page->getChild('testInput');

    $this->assertEqual($input->getAttribute('name'), 'testForm[TESTINPUT]');
  }

  function testChildFormElementsComplicatedNamesWrapping()
  {
    $template = '<limb:FORM id="testForm" name="testForm">
                    <input id="testInput" name="wow[wrap][testInput]" type="text">
                </limb:FORM>';

    RegisterTestingTemplate('/limb/form4.html', $template);

    $page =& new Template('/limb/form4.html');

    $input =& $page->getChild('testInput');

    $this->assertEqual($input->getAttribute('name'), 'testForm[wow][wrap][testInput]');
  }

  function testChildFormElementsNamesRecursiveWrapping()
  {
    $template = '<limb:FORM id="testForm" name="testForm">
                     <div id="wow" runat="server">
                     <input id="testInput" name="testInput" type="text">
                    </div>
                </limb:FORM>';

    RegisterTestingTemplate('/limb/form5.html', $template);

    $page =& new Template('/limb/form5.html');

    $input =& $page->getChild('testInput');

    $this->assertEqual($input->getAttribute('name'), 'testForm[testInput]');
  }

  function testRenderAsCommonForm()
  {
    $template = '<limb:FORM id="testForm" name="testForm"></limb:FORM>';

    RegisterTestingTemplate('/limb/form6.html', $template);

    $page =& new Template('/limb/form6.html');
    $result = $page->capture();

    $this->assertEqual($result, '<form id="testForm" name="testForm"></form>');
  }
}
?>
