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
require_once(LIMB_DIR . '/core/commands/RedirectToParentObjectCommand.class.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
require_once(LIMB_DIR . '/core/commands/Command.interface.php');
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                 'LimbBaseToolkitRedirectToParentObjectCommandTestVersion',
                 array('getPath2IdTranslator'));

Mock :: generate('Path2IdTranslator');
Mock :: generate('Command');

Mock :: generatePartial('RedirectToParentObjectCommand',
                        'RedirectToParentObjectCommandTestVersion',
                        array('getRedirectCommand'));


class RedirectToParentObjectCommandTest extends LimbTestCase
{
  var $path2id_translator;
  var $db;

  function RedirectToParentObjectCommandTest()
  {
    parent :: LimbTestCase('redirect to parent object command test');
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDbConnection();
    $this->db = new SimpleDb($conn);

    $this->toolkit = new LimbBaseToolkitRedirectToParentObjectCommandTestVersion($this);
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

  function _cleanUp()
  {
    $this->db->delete('sys_object_to_node');
  }

  function testPerformOk()
  {
    $object = new Object();
    $object->set('oid', $id = 10);
    $object->set('parent_node_id', $parent_node_id = 100);

    $this->toolkit->setMappedObject($object);

    $this->db->insert('sys_object_to_node', array('oid' => $parent_oid = 5,
                                                  'node_id' => $parent_node_id));

    $this->path2id_translator->expectOnce('toPath', array($parent_oid));
    $this->path2id_translator->setReturnValue('toPath', $path = 'any path');

    $regirect_command = new MockCommand($this);
    $regirect_command->expectOnce('perform');
    $regirect_command->setReturnValue('perform', LIMB_STATUS_OK);

    $command = new RedirectToParentObjectCommandTestVersion($this);

    $command->setReturnReference('getRedirectCommand', $regirect_command, array($path));

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $regirect_command->tally();
  }

  function testPerformOkRedirectToRootPage()
  {
    $object = new Object();
    $object->set('oid', $id = 10);
    $object->set('parent_node_id', $id = 100);
    $this->toolkit->setMappedObject($object);

    $this->db->delete('sys_object_to_node');

    $this->path2id_translator->expectNever('toPath');

    $regirect_command = new MockCommand($this);
    $regirect_command->expectOnce('perform');
    $regirect_command->setReturnValue('perform', LIMB_STATUS_OK);

    $command = new RedirectToParentObjectCommandTestVersion($this);

    $command->setReturnReference('getRedirectCommand', $regirect_command, array('/'));

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $regirect_command->tally();
  }
}

?>