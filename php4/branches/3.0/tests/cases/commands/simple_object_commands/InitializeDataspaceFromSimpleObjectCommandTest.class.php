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
require_once(dirname(__FILE__) . '/simple_object_commands_orm_support.inc.php');

class InitializeDataspaceFromSimpleObjectCommandStub extends InitializeDataspaceFromSimpleObjectCommand
{
  var $mock;

  function &_defineObjectHandle()
  {
    return $this->mock;
  }

  function _defineObject2DataspaceMap()
  {
    //object's getter => dataspace
    return array('getTitle' => 'title',
                 'getAnnotation' => 'annotation');
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
    $this->object = new SpecialMockSimpleObject($this);
    $this->object->SimpleObject();//dataspace init

    $this->cmd = new InitializeDataspaceFromSimpleObjectCommandStub();

    Limb :: saveToolkit();
  }

  function tearDown()
  {
    $this->object->tally();
    Limb :: restoreToolkit();
  }

  function testPerformOK()
  {
    $this->object->set('id', $id = 1001);
    $this->object->expectOnce('getTitle');
    $this->object->setReturnValue('getTitle', $title = 'title');
    $this->object->expectOnce('getAnnotation');
    $this->object->setReturnValue('getAnnotation', $annotation = 'annotation');

    $this->cmd->mock =& $this->object;

    $toolkit =& Limb :: toolkit();

    $uow =& $toolkit->getUOW();
    $uow->register($this->object);

    $request =& $toolkit->getRequest();
    $request->set('id', $id);

    $this->assertEqual($this->cmd->perform(), LIMB_STATUS_OK);

    $dataspace =& $toolkit->getDataspace();
    $this->assertEqual($dataspace->get('title'), $title);
    $this->assertEqual($dataspace->get('annotation'), $annotation);
  }

  function testPerformError()
  {
    $this->object->set('id', $id = 1001);
    $this->object->expectNever('getTitle');
    $this->object->expectNever('getAnnotation');

    $this->cmd->mock =& $this->object;

    $toolkit =& Limb :: toolkit();

    $uow =& $toolkit->getUOW();
    $uow->register($this->object);

    $request =& $toolkit->getRequest();
    $request->set('id', $id = 'no-such-object');

    $this->assertEqual($this->cmd->perform(), LIMB_STATUS_ERROR);
  }

}

?>
