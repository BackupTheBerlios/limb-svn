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

class LimbParameterTagTestCase extends LimbTestCase
{
  function LimbParameterTagTestCase()
  {
    parent :: LimbTestCase('limb parameter tag case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testSetParameter()
  {
    $template = '<div id="test" runat="server"><limb:PARAMETER limit="10" offset="20"></div>';

    RegisterTestingTemplate('/limb/parameter.html', $template);

    $page =& new Template('/limb/parameter.html');

    $div =& $page->getChild('test');

    $this->assertEqual($div->getAttribute('limit'), 10);
    $this->assertEqual($div->getAttribute('offset'), 20);

    $this->assertEqual('<div id="test" limit="10" offset="20"></div>',
                       $page->capture());
  }

}
?>
