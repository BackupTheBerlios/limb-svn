<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: RedirectCommandTest.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/RedirectToNodeCommand.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/entity/Entity.class.php');
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
require_once(LIMB_DIR . '/core/commands/Command.interface.php');
require_once(LIMB_DIR . '/core/NodeConnection.class.php');

Mock :: generate('LimbBaseToolkit');
Mock :: generate('Path2IdTranslator');
Mock :: generate('Command');

Mock :: generatePartial('RedirectToNodeCommand',
                        'RedirectToNodeCommandTestVersion',
                        array('getRedirectCommand'));


class RedirectToNodeCommandTest extends LimbTestCase
{
  var $path2id_translator;

  function RedirectToNodeCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit = new MockLimbBaseToolkit($this);
    $this->path2id_translator = new MockPath2IdTranslator($this);
    $this->toolkit->setReturnReference('getPath2IdTranslator', $this->path2id_translator);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->path2id_translator->tally();
    $this->toolkit->tally();

    Limb :: restoreToolkit();
  }

  function testPerformFailedNoNodeEntityPart()
  {
    $context = new DataSpace();

    $entity = new Entity();

    $context->setObject($entity_name = 'whatever', $entity);

    $this->path2id_translator->expectNever('getPathToNode');

    $command = new RedirectToNodeCommand($entity_name);

    $this->assertEqual($command->perform($context), LIMB_STATUS_ERROR);
  }

  function testPerformFailedNoEntity()
  {
    $context = new DataSpace();

    $this->path2id_translator->expectNever('getPathToNode');

    $command = new RedirectToNodeCommand($entity_name = 'whatever');

    $this->assertEqual($command->perform($context), LIMB_STATUS_ERROR);
  }

  function testPerformOk()
  {
    $context = new DataSpace();

    $node = new NodeConnection();
    $node->set('id', $node_id = 10);

    $entity = new Entity();
    $entity->registerPart('node', $node);

    $context->setObject($entity_name = 'whatever', $entity);

    $this->path2id_translator->expectOnce('getPathToNode', array($node_id));
    $this->path2id_translator->setReturnValue('getPathToNode', $path = 'any path');

    $regirect_command = new MockCommand($this);
    $regirect_command->expectOnce('perform', array($context));
    $regirect_command->setReturnValue('perform', LIMB_STATUS_OK);

    $command = new RedirectToNodeCommandTestVersion($this);
    $command->RedirectToNodeCommand($entity_name);

    $command->setReturnReference('getRedirectCommand', $regirect_command, array($path));

    $this->assertEqual($command->perform($context), LIMB_STATUS_OK);

    $regirect_command->tally();
  }
}

?>