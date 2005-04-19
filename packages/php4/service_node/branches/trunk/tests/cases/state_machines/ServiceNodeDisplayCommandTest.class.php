<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ImageObjectsDAOTest.class.php 1093 2005-02-07 15:17:20Z pachanga $
*
***********************************************************************************/
require_once(LIMB_SERVICE_NODE_DIR . '/state_machines/ServiceNodeDisplayCommand.class.php');

class ServiceNodeDisplayCommandTest extends LimbTestCase
{
  function ServiceNodeDisplayCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    Limb :: saveToolkit();
  }

  function tearDown()
  {
    for($i=0;$i<5;$i++)restore_error_handler();trigger_error('!', E_USER_WARNING);
    Limb :: restoreToolkit();
  }

  function testPerform()
  {
    $command = new ServiceNodeDisplayCommand();

    $this->assertEqual($command->perform(new Dataspace()), LIMB_STATUS_OK);

    $this->assertEqual($command->getStateHistory(), array(
                                                     array('initial' => LIMB_STATUS_OK),
                                                     array('render' =>  LIMB_STATUS_OK)));
  }
}

?>