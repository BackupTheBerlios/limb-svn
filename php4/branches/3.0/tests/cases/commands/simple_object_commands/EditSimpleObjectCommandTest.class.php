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
require_once(LIMB_DIR . '/core/commands/EditSimpleObjectCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object_commands_orm_support.inc.php');

class EditSimpleObjectCommandStub extends EditSimpleObjectCommand
{
  var $mock;

  function &_defineObjectHandle()
  {
    return $this->mock;
  }

  function _defineDataspace2ObjectMap()
  {
    //dataspace => object's setter
    return array('title' => 'setTitle',
                 'annotation' => 'setAnnotation');
  }
}

Mock :: generatePartial('SimpleObject',
                 'MockSimpleObject',
                 array('setTitle', 'setAnnotation'));

class EditSimpleObjectCommandTest extends LimbTestCase
{
  var $cmd;

  function EditSimpleObjectCommandTest()
  {
    parent :: LimbTestCase('edit simple cms object command test');
  }

  function setUp()
  {
    $this->object = new MockSimpleObject($this);
    $this->object->SimpleObject();//dataspace init

    $this->cmd = new EditSimpleObjectCommandStub();

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
    $this->object->expectOnce('setTitle', array($title = 'title'));
    $this->object->expectOnce('setAnnotation', array($annotation = 'annotation'));

    $this->cmd->mock =& $this->object;

    $toolkit =& Limb :: toolkit();

    $uow =& $toolkit->getUOW();
    $uow->register($this->object);

    $request =& $toolkit->getRequest();
    $request->set('id', $id);

    $dataspace =& $toolkit->getDataspace();
    $dataspace->set('title', $title);
    $dataspace->set('annotation', $annotation);

    $this->assertEqual($this->cmd->perform(), LIMB_STATUS_OK);
  }

  function testPerformError()
  {
    $this->object->set('id', $id = 1001);
    $this->object->expectNever('setTitle');
    $this->object->expectNever('setAnnotation');

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
