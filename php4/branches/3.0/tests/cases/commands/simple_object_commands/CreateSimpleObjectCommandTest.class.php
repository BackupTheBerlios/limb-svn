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
require_once(dirname(__FILE__) . '/simple_object.inc.php');

class CreateSimpleObjectCommandStub extends CreateSimpleObjectCommand
{
  var $object;

  function &_defineObjectHandle()
  {
    return $this->object;
  }

  function _defineDataspace2ObjectMap()
  {
    //dataspace => object's setter
    return array('title' => 'title',
                 'annotation' => 'annotation',
                 'content' => 'content');
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
    $this->cmd = new CreateSimpleObjectCommandStub();

    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testPerform()
  {
    $object = new SimpleObject();

    $this->cmd->object =& $object;

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $dataspace->set('title', $title = 'title');
    $dataspace->set('annotation', $annotation = 'annotation');
    $dataspace->set('content', $content = '');

    $this->assertEqual($this->cmd->perform(), LIMB_STATUS_OK);

    $uow =& $toolkit->getUOW();

    $this->assertTrue($uow->isRegistered($object));

    $this->assertEqual($object->get('title'), $title);
    $this->assertEqual($object->get('annotation'), $annotation);
    $this->assertIdentical($object->get('content'), $content);
  }

}

?>
