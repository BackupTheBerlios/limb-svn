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

class CreateSimpleObjectCommandTest extends LimbTestCase
{
  function CreateSimpleObjectCommandTest()
  {
    parent :: LimbTestCase('create simple cms object command test');
  }

  function setUp()
  {
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testPerform()
  {
    $object = new SimpleObject();

    //dataspace field => object's fields
    $map = array('ds_title' => 'title',
                 'ds_annotation' => 'annotation',
                 'ds_content' => 'content');

    $command = new CreateSimpleObjectCommand($map, $object);

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $dataspace->set('ds_title', $title = 'title');
    $dataspace->set('ds_annotation', $annotation = 'annotation');
    $dataspace->set('ds_content', $content = '');

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $uow =& $toolkit->getUOW();

    $this->assertTrue($uow->isRegistered($object));

    $this->assertEqual($object->get('title'), $title);
    $this->assertEqual($object->get('annotation'), $annotation);
    $this->assertIdentical($object->get('content'), $content);
  }
}

?>
