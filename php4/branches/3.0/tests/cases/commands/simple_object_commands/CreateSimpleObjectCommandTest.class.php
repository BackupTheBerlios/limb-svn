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
require_once(LIMB_DIR . '/core/commands/CreateSimpleObjectCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object_commands_orm_support.inc.php');

class CreateSimpleObjectCommandStub extends CreateSimpleObjectCommand
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


class CreateSimpleObjectCommandTest extends LimbTestCase
{
  var $cmd;

  function CreateSimpleObjectCommandTest()
  {
    parent :: LimbTestCase('create simple cms object command test');
  }

  function setUp()
  {
    $this->object = new SpecialMockSimpleObject($this);
    $this->object->SimpleObject();//dataspace init

    $this->cmd = new CreateSimpleObjectCommandStub();

    Limb :: saveToolkit();
  }

  function tearDown()
  {
    $this->object->tally();
    Limb :: restoreToolkit();
  }

  function testPerform()
  {
    $this->object->expectOnce('setTitle', array($title = 'title'));
    $this->object->expectOnce('setAnnotation', array($annotation = 'annotation'));

    $this->cmd->mock =& $this->object;

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $dataspace->set('title', $title);
    $dataspace->set('annotation', $annotation);

    $this->assertEqual($this->cmd->perform(), LIMB_STATUS_OK);

    $uow =& $toolkit->getUOW();

    $this->assertTrue($uow->isRegistered($this->object));
  }

}

?>
