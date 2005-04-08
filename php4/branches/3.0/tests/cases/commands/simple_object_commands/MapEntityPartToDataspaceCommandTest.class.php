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
require_once(LIMB_DIR . '/core/commands/MapEntityPartToDataspaceCommand.class.php');
require_once(LIMB_DIR . '/core/entity/Entity.class.php');
require_once(dirname(__FILE__) . '/simple_object.inc.php');

class MapEntityPartToDataspaceCommandTest extends LimbTestCase
{
  var $cmd;

  function MapEntityPartToDataspaceCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testPerformFailedNoSuchContextEntry()
  {
    $context = new DataSpace();
    $command = new MapEntityPartToDataspaceCommand($map = array(), $context_key = 'whatever', $part_key = 'key');
    $this->assertEqual($command->perform($context), LIMB_STATUS_ERROR);
  }

  function testPerformFailedNoSuchEntityPart()
  {
    $context = new DataSpace();
    $entity = new Entity();

    $context->setObject($context_key = 'whatever', $entity);

    $command = new MapEntityPartToDataspaceCommand($map = array(), $context_key, $part_key = 'none');
    $this->assertEqual($command->perform($context), LIMB_STATUS_ERROR);
  }

  function testPerformOK()
  {
    $toolkit =& Limb :: toolkit();

    $part = new Object();

    $part->set('id', $id = 1001);
    $part->set('title', $title = 'title');
    $part->set('annotation', $annotation = 'annotation');
    $part->set('content', $content = '');

    $entity = new Entity();
    $entity->registerPart($part_key = 'part', $part);

    //object's field => dataspace field
    $map = array('title' => 'ds_title',
                 'annotation' => 'ds_annotation',
                 'content' => 'ds_content');

    $command = new MapEntityPartToDataspaceCommand($map, $context_key = 'whatever', $part_key);

    $context = new DataSpace();
    $context->setObject($context_key = 'whatever', $entity);

    $this->assertEqual($command->perform($context), LIMB_STATUS_OK);

    $dataspace =& $toolkit->getDataspace();
    $this->assertEqual($dataspace->get('ds_title'), $title);
    $this->assertEqual($dataspace->get('ds_annotation'), $annotation);
    $this->assertIdentical($dataspace->get('ds_content'), $content);
  }
}

?>
