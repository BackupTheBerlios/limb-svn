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
require_once(dirname(__FILE__) . '/simple_object.inc.php');

class EditSimpleObjectCommandStub extends EditSimpleObjectCommand
{
  function &_defineObjectHandle()
  {
    return new LimbHandle('SimpleObject');
  }

  function _defineDataspace2ObjectMap()
  {
    //dataspace => object's setter
    return array('title' => 'title',
                 'annotation' => 'annotation',
                 'content' => 'content');
  }
}

class EditSimpleObjectCommandTest extends LimbTestCase
{
  var $cmd;

  function EditSimpleObjectCommandTest()
  {
    parent :: LimbTestCase('edit simple cms object command test');
  }

  function setUp()
  {
    $this->cmd = new EditSimpleObjectCommandStub();

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

    $toolkit =& Limb :: toolkit();

    $uow =& $toolkit->getUOW();
    $uow->register($object);

    $request =& $toolkit->getRequest();
    $request->set('id', $id);

    $dataspace =& $toolkit->getDataspace();
    $dataspace->set('title', $title = 'title');
    $dataspace->set('annotation', $annotation = 'annotation');
    $dataspace->set('content', $content = '');

    $this->assertEqual($this->cmd->perform(), LIMB_STATUS_OK);

    $this->assertEqual($object->get('title'), $title);
    $this->assertEqual($object->get('annotation'), $annotation);
    $this->assertIdentical($object->get('content'), $content);
  }

  function testPerformError()
  {
    $object = new SimpleObject();
    $object->set('id', $id = 1001);

    $toolkit =& Limb :: toolkit();

    $uow =& $toolkit->getUOW();
    $uow->register($object);

    $request =& $toolkit->getRequest();
    $request->set('id', $id = 'no-such-object');

    $this->assertEqual($this->cmd->perform(), LIMB_STATUS_ERROR);

    $this->assertFalse($object->get('title'));
    $this->assertFalse($object->get('annotation'));
  }

}

?>
