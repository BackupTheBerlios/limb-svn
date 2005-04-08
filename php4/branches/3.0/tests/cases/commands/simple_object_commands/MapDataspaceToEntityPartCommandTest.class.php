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
require_once(LIMB_DIR . '/core/commands/MapDataspaceToEntityPartCommand.class.php');
require_once(dirname(__FILE__) . '/simple_object.inc.php');

class MapDataspaceToEntityPartCommandTest extends LimbTestCase
{
  function MapDataspaceToEntityPartCommandTest()
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
    $command = new MapDataspaceToEntityPartCommand($map = array(), $context_key = 'whatever', $part_key = 'key');
    $this->assertEqual($command->perform($context), LIMB_STATUS_ERROR);
  }

  function testPerformFailedNoSuchEntityPart()
  {
    $context = new DataSpace();
    $entity = new Entity();

    $context->setObject($context_key = 'whatever', $entity);

    $command = new MapDataspaceToEntityPartCommand($map = array(), $context_key, $part_key = 'none');
    $this->assertEqual($command->perform($context), LIMB_STATUS_ERROR);
  }

  function testPerformOK()
  {
    //dataspace field => object's fields
    $map = array('ds_title' => 'title',
                 'ds_annotation' => 'annotation',
                 'ds_content' => 'content',
                 'any_field' => 'any_field');

    $toolkit =& Limb :: toolkit();

    $part = new Object();

    $entity = new Entity();
    $entity->registerPart($part_name = 'part', $part);

    $command = new MapDataspaceToEntityPartCommand($map, $context_key = 'key', $part_name);

    $dataspace =& $toolkit->getDataspace();
    $dataspace->set('ds_title', $title = 'title');
    $dataspace->set('ds_annotation', $annotation = 'annotation');
    $dataspace->set('ds_content', $content = '');

    $context = new DataSpace();
    $context->setObject($context_key, $entity);

    $this->assertEqual($command->perform($context), LIMB_STATUS_OK);

    $this->assertEqual($part->get('title'), $title);
    $this->assertEqual($part->get('annotation'), $annotation);
    $this->assertIdentical($part->get('content'), $content);
    $values = $part->export();
    $this->assertFalse(array_key_exists('any_field', $values));
  }
}

?>
