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
require_once(LIMB_DIR . '/class/core/commands/display_view_command.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/request/response.interface.php');
require_once(LIMB_DIR . '/class/template/template.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('response');

class template_stub extends template
{
  function __construct()
  {
  }
  
  public function display()
	{
    echo 'test template';
	} 
}

class display_view_command_test extends LimbTestCase 
{
  var $toolkit;
  var $response;
  var $template;
		  	
  function setUp()
  {    
    $this->toolkit = new MockLimbToolkit($this);
    $this->response = new Mockresponse($this);
    $this->template = new template_stub();
    
    $this->toolkit->setReturnValue('getResponse', $this->response);
     
    Limb :: registerToolkit($this->toolkit);
  }
  
  function tearDown()
  { 
    Limb :: popToolkit();
    
  	$this->toolkit->tally();
    $this->response->tally();
  }
          
  function test_perform_ok()
  {
    $command = new display_view_command();
        
    $this->toolkit->expectOnce('getView');
    $this->toolkit->setReturnValue('getView', $this->template);
    
    $this->response->expectOnce('write', array('test template'));
    
  	$this->assertEqual($command->perform(), Limb :: STATUS_OK);
  }  

  function test_perform_failed_no_view()
  {
    $command = new display_view_command();
        
    $this->toolkit->expectOnce('getView');
    $this->toolkit->setReturnValue('getView', null);
     
    try
    {
      $command->perform();
      $this->assertTrue(false);
    }
    catch(LimbException $e)
    {
    }
  }  
}

?>