<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbParameterTagTest.class.php 1013 2005-01-12 12:13:22Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');

class LimbOptionalTagTestCase extends LimbTestCase
{
  function LimbOptionalTagTestCase()
  {
    parent :: LimbTestCase('limb optional tag case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testValueIsSet()
  {
    $template = '<limb:OPTIONAL for="var">test!</limb:OPTIONAL>';

    RegisterTestingTemplate('/limb/optional_is_set.html', $template);

    $page =& new Template('/limb/optional_is_set.html');

    $page->set('var', 'any_value');

    $this->assertEqual('test!', $page->capture());
  }

  function testValueIsZero()
  {
    $template = '<limb:OPTIONAL for="var">test!</limb:OPTIONAL>';

    RegisterTestingTemplate('/limb/optional_is_zero.html', $template);

    $page =& new Template('/limb/optional_is_zero.html');

    $page->set('var', 0);

    $this->assertEqual('', $page->capture());
  }

}
?>
