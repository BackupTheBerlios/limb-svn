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

class LimbIpFilterTestCase extends LimbTestCase
{
  function LimbIpFilterTestCase()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testFilter()
  {
    $template = '{$"ffffffff"|ip}';

    RegisterTestingTemplate('/limb/ip.html', $template);

    $page =& new Template('/limb/ip.html');

    $this->assertEqual($page->capture(), '255.255.255.255');
  }

  function testFilterDBE()
  {
    $template = '<core:SET var="ffffffff">{$var|ip}';

    RegisterTestingTemplate('/limb/ip.html', $template);

    $page =& new Template('/limb/ip.html');

    $this->assertEqual($page->capture(), '255.255.255.255');
  }

}
?>
