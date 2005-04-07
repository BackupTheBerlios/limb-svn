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
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');

Mock :: generate('LimbBaseToolkit');
Mock :: generate('Path2IdTranslator');

class LimbPathRecordSetProcessorTagTestCase extends LimbTestCase
{
  var $toolkit;
  var $path2id_translator;

  function LimbPathRecordSetProcessorTagTestCase()
  {
    parent :: LimbTestCase('limb path record set processor tag case');
  }

  function setUp()
  {
    $this->path2id_translator = new MockPath2IdTranslator($this);
    $this->toolkit = new MockLimbBaseToolkit($this);
    $this->toolkit->setReturnReference('getPath2IdTranslator', $this->path2id_translator);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->path2id_translator->tally();
    $this->toolkit->tally();

    Limb :: restoreToolkit();

    ClearTestingTemplates();
  }

  function testTag()
  {
    $object1_data = array('_tree_id' => $id1 = 10);
    $object2_data = array('_tree_id' => $id2 = 20);

    $rs = new PagedArrayDataset(array($object1_data, $object2_data));

    $this->path2id_translator->expectAtleastOnce('getPathToNode');
    $this->path2id_translator->setReturnValueAt(0, 'getPathToNode', $path1 = 'path1');
    $this->path2id_translator->setReturnValueAt(1, 'getPathToNode', $path2 = 'path2');

    $template = '<limb:recordset_processor:PATH source="list1">'.
                 '<list:LIST id="list1">'.
                   '<list:ITEM>{$path}_{$_tree_id}|</list:ITEM>'.
                 '</list:LIST>';

    RegisterTestingTemplate('/limb/path_record_set_processor.html', $template);

    $page =& new Template('/limb/path_record_set_processor.html');
    $component =& $page->findChild('list1');

    $component->registerDataSet($rs);

    $this->assertEqual($page->capture(), $path1.'_10|'. $path2 . '_20|');
  }

}
?>
