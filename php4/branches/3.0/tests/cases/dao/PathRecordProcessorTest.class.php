<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: TreeBranchCriteriaTest.class.php 1241 2005-04-19 11:59:49Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
require_once(LIMB_DIR . '/core/dao/processors/PathRecordProcessor.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                        'ToolkitPathRecordProcessorTestVersion',
                        array('getPath2IdTranslator'));

Mock :: generate('Path2IdTranslator');

class PathRecordProcessorTest extends LimbTestCase
{
  var $toolkit;
  var $translator;

  function PathRecordProcessorTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit = new ToolkitPathRecordProcessorTestVersion($this);
    $this->translator = new MockPath2IdTranslator($this);
    $this->toolkit->setReturnReference('getPath2IdTranslator', $this->translator);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->translator->tally();

    Limb :: restoreToolkit();
  }

  function testProcessUseParentNodeId()
  {
    $record = new DataSpace();
    $record->import(array('_node_parent_id' => $parent_node_id = 10,
                    '_node_id' => $node_id = 100,
                    '_node_identifier' => $identifier = 'item1'));

    $this->translator->expectOnce('getPathToNode', array($parent_node_id));
    $this->translator->setReturnValue('getPathToNode', $path = '/whatever/');

    $processor = new PathRecordProcessor();
    $processor->process($record);

    $this->assertEqual($record->get('_node_path'), $path . $identifier);
  }

  function testProcessUseNodeId()
  {
    $record = new DataSpace();
    $record->import(array('_node_id' => $node_id = 100));

    $this->translator->expectOnce('getPathToNode', array($node_id));
    $this->translator->setReturnValue('getPathToNode', $path = '/whatever/');

    $processor = new PathRecordProcessor();
    $processor->process($record);

    $this->assertEqual($record->get('_node_path'), $path);
  }
}
?>
