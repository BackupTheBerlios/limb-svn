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
require_once(LIMB_DIR . '/core/commands/RedirectToParentNodeCommand.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
require_once(LIMB_DIR . '/core/commands/Command.interface.php');
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                 'LimbBaseToolkitRedirectToParentNodeCommandTestVersion',
                 array('getPath2IdTranslator'));

Mock :: generate('Path2IdTranslator');
Mock :: generate('Command');

Mock :: generatePartial('RedirectToParentNodeCommand',
                        'RedirectToParentNodeCommandTestVersion',
                        array('getRedirectCommand'));


class RedirectToParentNodeCommandTest extends LimbTestCase
{
  var $path2id_translator;

  function RedirectToParentNodeCommandTest()
  {
    parent :: LimbTestCase('redirect to parent node command test');
  }

  function setUp()
  {
    $this->toolkit = new LimbBaseToolkitRedirectToParentNodeCommandTestVersion($this);
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

  function testPerformOk()
  {
    $node = new Object();
    $node->set('parent_id', $parent_node_id = 100);

    $this->toolkit->setCurrentEntity($object);

    $this->path2id_translator->expectOnce('getPathToNode', array($parent_node_id));
    $this->path2id_translator->setReturnValue('getPathToNode', $path = 'any path');

    $regirect_command = new MockCommand($this);
    $regirect_command->expectOnce('perform');
    $regirect_command->setReturnValue('perform', LIMB_STATUS_OK);

    $command = new RedirectToParentNodeCommandTestVersion($this);
    $command->RedirectToParentNodeCommand($node);

    $command->setReturnReference('getRedirectCommand', $regirect_command, array($path));

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $regirect_command->tally();
  }
}

?>