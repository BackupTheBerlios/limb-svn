<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: LimbRepeatTagTest.class.php 1017 2005-01-13 12:10:15Z pachanga $
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(WACT_ROOT . '/iterator/pagedarraydataset.inc.php');

class LimbJIPRecordSetProcessorTagTestCase extends LimbTestCase
{
  function LimbJIPRecordSetProcessorTagTestCase()
  {
    parent :: LimbTestCase('limb jip record set processor tag case');
  }

  function tearDown()
  {
    ClearTestingTemplates();
  }

  function testTag()
  {
    $object1_data = array('path' => '/path1/',
                          'id' => 1,
                          'actions' => array('create' => array('jip' => true),
                                              'edit' => array('jip' => true)));

    $object2_data = array('path' => $path1 = '/path2/',
                          'id' => 2,
                          'actions' => array('create' => array('jip' => true),
                                              'edit' => array('jip' => true)));

    $rs = new PagedArrayDataset(array($object1_data, $object2_data));

    $template = '<limb:recordset_processor:JIP source="list1"><list:LIST id="list1">'.
                  '<list:ITEM>'.
                    '<list:LIST from="jip_actions"><list:ITEM>'.
                      '<core:OPTIONAL for="jip">{$name}-{$jip_href}_{$^id}|</core:OPTIONAL>'.
                     '</list:ITEM></list:LIST>'.
                   '</list:ITEM>'.
                 '</list:LIST>';

    RegisterTestingTemplate('/limb/jip_record_set_processor.html', $template);

    $page =& new Template('/limb/jip_record_set_processor.html');
    $component =& $page->findChild('list1');

    $component->registerDataSet($rs);

    $this->assertEqual($page->capture(), 'create-/path1/?action=create_1|'.
                                         'edit-/path1/?action=edit_1|'.
                                         'create-/path2/?action=create_2|'.
                                         'edit-/path2/?action=edit_2|');
  }

}
?>
