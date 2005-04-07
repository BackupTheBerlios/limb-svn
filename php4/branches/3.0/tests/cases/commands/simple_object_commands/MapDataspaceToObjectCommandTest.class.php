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
require_once(LIMB_DIR . '/core/commands/MapDataspaceToObjectCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object.inc.php');

class MapDataspaceToObjectCommandTest extends LimbTestCase
{
  function MapDataspaceToObjectCommandTest()
  {
    parent :: LimbTestCase('map dataspace to object command test');
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
                 'ds_content' => 'content',
                 'any_field' => 'any_field');

    $toolkit =& Limb :: toolkit();

    $object = new SimpleObject();

    $command = new MapDataspaceToObjectCommand($map, $object);

    $dataspace =& $toolkit->getDataspace();
    $dataspace->set('ds_title', $title = 'title');
    $dataspace->set('ds_annotation', $annotation = 'annotation');
    $dataspace->set('ds_content', $content = '');
    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $this->assertEqual($object->get('title'), $title);
    $this->assertEqual($object->get('annotation'), $annotation);
    $this->assertIdentical($object->get('content'), $content);
    $values = $object->export();
    $this->assertFalse(array_key_exists('any_field', $values));
  }
}

?>
