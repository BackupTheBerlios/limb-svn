<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/core/commands/use_view_command.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');

Mock :: generate('LimbToolkit');

class use_view_command_test extends LimbTestCase 
{
  var $toolkit;
		  	
  function setUp()
  {    
    $this->toolkit = new MockLimbToolkit($this);
     
    Limb :: registerToolkit($this->toolkit);
  }
  
  function tearDown()
  { 
    Limb :: popToolkit();
    
  	$this->toolkit->tally();
  }
          
  function test_perform_ok()
  {
    $command = new use_view_command('/test.html');
    
    $handle = array(LIMB_DIR . '/class/template/template', '/test.html');
    
    $this->toolkit->expectOnce('setView', array($handle));
    
  	$this->assertEqual($command->perform(), Limb :: STATUS_OK);
  }  
}

?>