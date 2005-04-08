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
require_once(LIMB_DIR . '/core/commands/InitParentNodeIdCommand.class.php');
require_once(LIMB_DIR . '/core/NodeConnection.class.php');


class InitParentNodeIdCommandTest extends LimbTestCase
{

  function InitParentNodeIdCommandTest()
  {
    parent :: LimbTestCase('init parent node id command test');
  }

  function setUp()
  {
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();
  }

  function testPerformOk()
  {
    $node = new NodeConnection();
    $node->set('id', $id = 10);

    $command = new InitParentNodeIdCommand($node);

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $this->assertEqual($dataspace->get('parent_node_id'), $id);
  }
}

?>