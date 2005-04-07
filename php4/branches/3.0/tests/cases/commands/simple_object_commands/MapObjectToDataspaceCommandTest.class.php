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
require_once(LIMB_DIR . '/core/commands/MapObjectToDataspaceCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object.inc.php');

class MapObjectToDataspaceCommandTest extends LimbTestCase
{
  var $cmd;

  function MapObjectToDataspaceCommandTest()
  {
    parent :: LimbTestCase('map object to dataspace command test');
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
    $toolkit =& Limb :: toolkit();

    $object = new SimpleObject();

    $object->set('id', $id = 1001);
    $object->set('title', $title = 'title');
    $object->set('annotation', $annotation = 'annotation');
    $object->set('content', $content = '');

    //object's field => dataspace field
    $map = array('title' => 'ds_title',
                 'annotation' => 'ds_annotation',
                 'content' => 'ds_content');

    $command = new MapObjectToDataspaceCommand($map, $object);

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $dataspace =& $toolkit->getDataspace();
    $this->assertEqual($dataspace->get('ds_title'), $title);
    $this->assertEqual($dataspace->get('ds_annotation'), $annotation);
    $this->assertIdentical($dataspace->get('ds_content'), $content);
  }
}

?>
