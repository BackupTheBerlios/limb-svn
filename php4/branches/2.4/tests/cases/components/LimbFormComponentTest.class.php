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
require_once(WACT_ROOT . 'template/template.inc.php');
require_once(WACT_ROOT . 'template/components/form/form.inc.php');

class LimbFormComponentTestCase extends LimbTestCase
{
  function LimbFormComponentTestCase()
  {
    parent :: LimbTestCase('limb form component case');
  }

  function testChildFormElementsNamesWrapping()
  {
    $template = '<limb:FORM id="testForm" name="testForm">
                    <label id="testLabel" name="label" for="testId">A label</label>
                    <input id="testInput" name="testInput" type="text">
                </limb:FORM>';

    RegisterTestingTemplate('/components/form/limbform.html', $template);

    $page =& new Template('/components/form/limbform.html');

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

    RegisterTestingTemplate('/components/form/limbform2.html', $template);

    $page =& new Template('/components/form/limbform2.html');

    $input =& $page->getChild('testInput');

    $this->assertEqual($input->getAttribute('name'), 'testForm[TESTINPUT]');
  }

  function testChildFormElementsNamesWrappingDBE()
  {
    $template = '<core:SET name="testinput">
                <limb:FORM id="testForm" name="testForm">
                    <input id="testInput" name="{$^name|uppercase}" type="text">
                </limb:FORM>';

    RegisterTestingTemplate('/components/form/limbform3.html', $template);

    $page =& new Template('/components/form/limbform3.html');

    $input =& $page->getChild('testInput');

    $this->assertEqual($input->getAttribute('name'), 'testForm[TESTINPUT]');
  }

  function testChildFormElementsComplicatedNamesWrapping()
  {
    $template = '<limb:FORM id="testForm" name="testForm">
                    <input id="testInput" name="wow[wrap][testInput]" type="text">
                </limb:FORM>';

    RegisterTestingTemplate('/components/form/limbform4.html', $template);

    $page =& new Template('/components/form/limbform4.html');

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

    RegisterTestingTemplate('/components/form/limbform5.html', $template);

    $page =& new Template('/components/form/limbform5.html');

    $input =& $page->getChild('testInput');

    $this->assertEqual($input->getAttribute('name'), 'testForm[testInput]');
  }
}
?>
