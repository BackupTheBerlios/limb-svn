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
require_once(WACT_ROOT . '/iterator/arraydataset.inc.php');

class LimbDataTransferTagTestCase extends LimbTestCase
{
  function LimbDataTransferTagTestCase()
  {
    parent :: LimbTestCase('limb data transfer tag case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testTransfer()
  {
    $data = array (
      array ('name'=> 'joe', 'children' => array(array('child' => 'enny'), array('child' => 'harry'))),
      array ('name'=> 'ivan', 'children' => array(array('child' => 'ann'), array('child' => 'boris'))),
    );

    $dataset =& new ArrayDataSet($data);

    $template = '<list:LIST id="fathers"><list:ITEM>{$name}|'.
                '<limb:DATA_TRANSFER from="children" target="children">' .
                '<list:LIST id="children"><list:ITEM>{$child},</list:ITEM></list:LIST>|</list:ITEM></list:LIST>';

    RegisterTestingTemplate('/limb/datatransfer.html', $template);

    $page =& new Template('/limb/datatransfer.html');

    $page->setChildDataSet('fathers', $dataset);

    $this->assertEqual($page->capture(), 'joe|enny,harry,|ivan|ann,boris,|');
  }
}
?>
