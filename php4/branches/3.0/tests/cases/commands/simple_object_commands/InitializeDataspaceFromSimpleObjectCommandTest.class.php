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
require_once(LIMB_DIR . '/core/commands/InitializeDataspaceFromSimpleObjectCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object.inc.php');

class InitializeDataspaceFromSimpleObjectCommandStub extends InitializeDataspaceFromSimpleObjectCommand
{
  function &_defineObjectHandle()
  {
    return new LimbHandle('SimpleObject');
  }

  function _defineObject2DataspaceMap()
  {
    //object's getter => dataspace
    return array('title' => 'title',
                 'annotation' => 'annotation',
                 'content' => 'content');
  }
}

class InitializeDataspaceFromSimpleObjectCommandTest extends LimbTestCase
{
  var $cmd;

  function InitializeDataspaceFromSimpleObjectCommandTest()
  {
    parent :: LimbTestCase('initialize dataspace from simple object command test');
  }

  function setUp()
  {
    $this->cmd = new InitializeDataspaceFromSimpleObjectCommandStub();

    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testPerformOK()
  {
    $object = new SimpleObject();

    $object->set('id', $id = 1001);
    $object->set('title', $title = 'title');
    $object->set('annotation', $annotation = 'annotation');
    $object->set('content', $content = '');

    $toolkit =& Limb :: toolkit();

    $uow =& $toolkit->getUOW();
    $uow->register($object);

    $request =& $toolkit->getRequest();
    $request->set('id', $id);

    $this->assertEqual($this->cmd->perform(), LIMB_STATUS_OK);

    $dataspace =& $toolkit->getDataspace();
    $this->assertEqual($dataspace->get('title'), $title);
    $this->assertEqual($dataspace->get('annotation'), $annotation);
    $this->assertIdentical($dataspace->get('content'), $content);
  }

  function testPerformError()
  {
    $object = new SimpleObject();

    $object->set('id', $id = 1001);
    $object->set('title', $title = 'title');
    $object->set('annotation', $annotation = 'annotation');

    $toolkit =& Limb :: toolkit();

    $uow =& $toolkit->getUOW();
    $uow->register($object);

    $request =& $toolkit->getRequest();
    $request->set('id', $id = 'no-such-object');

    $this->assertEqual($this->cmd->perform(), LIMB_STATUS_ERROR);

    $dataspace =& $toolkit->getDataspace();
    $this->assertFalse($dataspace->get('title'));
    $this->assertFalse($dataspace->get('annotation'));
  }

}

?>
