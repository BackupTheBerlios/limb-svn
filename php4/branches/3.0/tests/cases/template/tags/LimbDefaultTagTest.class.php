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

class LimbDefaultTagTestCase extends LimbTestCase
{
  function LimbDefaultTagTestCase()
  {
    parent :: LimbTestCase('limb default tag case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testValueIsSet()
  {
    $template = '<limb:DEFAULT for="var">test!</limb:DEFAULT>';

    RegisterTestingTemplate('/limb/default_is_set.html', $template);

    $page =& new Template('/limb/default_is_set.html');

    $page->set('var', 'any_value');

    $this->assertEqual('', $page->capture());
  }

  function testValueIsZero()
  {
    $template = '<limb:DEFAULT for="var">test!</limb:DEFAULT>';

    RegisterTestingTemplate('/limb/default_is_zero.html', $template);

    $page =& new Template('/limb/default_is_zero.html');

    $page->set('var', 0);

    $this->assertEqual('test!', $page->capture());
  }

}
?>
