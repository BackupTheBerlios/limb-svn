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

class EditSimpleObjectCommandTest extends LimbTestCase
{
  function EditSimpleObjectCommandTest()
  {
    parent :: LimbTestCase('edit simple cms object command test');
  }

  function setUp()
  {
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testPerformOK()
  {
    //dataspace field => object's fields
    $map = array('ds_title' => 'title',
                 'ds_annotation' => 'annotation',
                 'ds_content' => 'content');

    $object = new SimpleObject();
    $object->set('id', $id = 1001);

    $command = new EditSimpleObjectCommand($map, $object);

    $toolkit =& Limb :: toolkit();

    $uow =& $toolkit->getUOW();
    $uow->register($object);

    $request =& $toolkit->getRequest();
    $request->set('id', $id);

    $dataspace =& $toolkit->getDataspace();
    $dataspace->set('ds_title', $title = 'title');
    $dataspace->set('ds_annotation', $annotation = 'annotation');
    $dataspace->set('ds_content', $content = '');

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $this->assertEqual($object->get('title'), $title);
    $this->assertEqual($object->get('annotation'), $annotation);
    $this->assertIdentical($object->get('content'), $content);
  }

  function testPerformError()
  {
    $object = new SimpleObject();
    $object->set('id', $id = 1001);

    $map = array('title' => 'title',
                 'annotation' => 'annotation',
                 'content' => 'content');

    $command = new EditSimpleObjectCommand($map, $object);

    $toolkit =& Limb :: toolkit();

    $uow =& $toolkit->getUOW();
    $uow->register($object);

    $request =& $toolkit->getRequest();
    $request->set('id', $id = 'no-such-object');

    $this->assertEqual($command->perform(), LIMB_STATUS_ERROR);

    $this->assertFalse($object->get('title'));
    $this->assertFalse($object->get('annotation'));
  }

}

?>
