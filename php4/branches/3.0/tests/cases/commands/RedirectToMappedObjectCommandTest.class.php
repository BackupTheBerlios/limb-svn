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
require_once(LIMB_DIR . '/core/commands/RedirectToMappedObjectCommand.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
require_once(LIMB_DIR . '/core/commands/Command.interface.php');

Mock :: generate('LimbBaseToolkit');
Mock :: generate('Path2IdTranslator');
Mock :: generate('Command');

Mock :: generatePartial('RedirectToMappedObjectCommand',
                        'RedirectToMappedObjectCommandTestVersion',
                        array('getRedirectCommand'));


class RedirectToMappedObjectCommandTest extends LimbTestCase
{
  var $path2id_translator;

  function RedirectToMappedObjectCommandTest()
  {
    parent :: LimbTestCase('redirect to mapped object command test');
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

  function testPerformOk()
  {
    $object = new Object();
    $object->set('oid', $id = 10);
    $this->toolkit->setReturnReference('getCurrentEntity', $object);

    $this->path2id_translator->expectOnce('toPath', array($id));
    $this->path2id_translator->setReturnValue('toPath', $path = 'any path');

    $regirect_command = new MockCommand($this);
    $regirect_command->expectOnce('perform');
    $regirect_command->setReturnValue('perform', LIMB_STATUS_OK);

    $command = new RedirectToMappedObjectCommandTestVersion($this);

    $command->setReturnReference('getRedirectCommand', $regirect_command, array($path));

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $regirect_command->tally();
  }
}

?>