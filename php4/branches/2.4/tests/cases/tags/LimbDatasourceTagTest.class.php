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

class LimbDatasourceTagTestCase extends LimbTestCase
{
  function LimbDatasourceTagTestCase()
  {
    parent :: LimbTestCase('limb datasource tag case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testChildFormElementsNamesWrapping()
  {
    $template = '<limb:DATASOURCE target="testTarget" datasource_path="TestDatasource">
                </limb:DATASOURCE>';

    RegisterTestingTemplate('/limb/datasource.html', $template);

    $page =& new Template('/limb/datasource.html');

    $input =& $page->getChild('testInput');
    $label =& $page->getChild('testLabel');

    $this->assertEqual($input->getAttribute('name'), 'testForm[testInput]');
    $this->assertEqual($label->getAttribute('name'), 'label');
  }

}
?>
