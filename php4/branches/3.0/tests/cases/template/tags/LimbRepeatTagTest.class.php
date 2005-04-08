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

class LimbRepeatTagTestCase extends LimbTestCase
{
  function LimbRepeatTagTestCase()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testRepeat()
  {
    $template = '<limb:REPEAT value="3">hello!</limb:REPEAT>';

    RegisterTestingTemplate('/limb/repeat.html', $template);

    $page =& new Template('/limb/repeat.html');

    $this->assertEqual($page->capture(), 'hello!hello!hello!');
  }

  function testRepeatByVariable()
  {
    $template = '<core:SET count="4"><limb:REPEAT value="{$count}">hello!</limb:REPEAT>';

    RegisterTestingTemplate('/limb/repeat2.html', $template);

    $page =& new Template('/limb/repeat2.html');

    $this->assertEqual($page->capture(), 'hello!hello!hello!hello!');
  }
}
?>
