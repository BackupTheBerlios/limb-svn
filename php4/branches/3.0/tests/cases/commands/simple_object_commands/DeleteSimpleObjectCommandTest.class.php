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
require_once(LIMB_DIR . '/core/commands/DeleteSimpleObjectCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object_commands_orm_support.inc.php');

class DeleteSimpleObjectCommandStub extends DeleteSimpleObjectCommand
{
  var $mock;

  function &_defineObjectHandle()
  {
    return $this->mock;
  }
}

class DeleteSimpleObjectCommandTest extends LimbTestCase
{
  var $cmd;

  function DeleteSimpleObjectCommandTest()
  {
    parent :: LimbTestCase('delete simple cms object command test');
  }

  function setUp()
  {
    $this->object = new SpecialMockSimpleObject($this);
    $this->object->SimpleObject();//dataspace init

    $this->cmd = new DeleteSimpleObjectCommandStub();

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
    $this->cmd->mock =& $this->object;

    $toolkit =& Limb :: toolkit();

    $uow =& $toolkit->getUOW();
    $uow->register($this->object);

    $request =& $toolkit->getRequest();
    $request->set('id', $id);

    $this->assertEqual($this->cmd->perform(), LIMB_STATUS_OK);

    $this->assertTrue($uow->isDeleted($this->object));
  }

  function testPerformError()
  {
    $this->cmd->mock =& $this->object;

    $toolkit =& Limb :: toolkit();

    $uow =& $toolkit->getUOW();
    $uow->register($this->object);

    $request =& $toolkit->getRequest();
    $request->set('id', $id = 1000);

    $this->assertEqual($this->cmd->perform(), LIMB_STATUS_ERROR);

    $this->assertFalse($uow->isDeleted($this->object));
  }
}

?>
