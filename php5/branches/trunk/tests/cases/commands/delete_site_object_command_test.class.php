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
require_once(LIMB_DIR . '/class/core/commands/delete_site_object_command.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/fetcher.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('request');
Mock :: generate('fetcher');
Mock :: generate('site_object');

class site_object_delete_command_test_version1 extends site_object
{
  public function delete()
  {
    throw new LimbException('catch me!');
  }
}

class site_object_delete_command_test_version2 extends site_object
{
  public function delete()
  {
    throw new SQLException('catch me!');
  }
}


class delete_site_object_command_test extends LimbTestCase 
{
	var $delete_command;
	var $site_object;
  var $request;
  var $toolkit;
  var $fetcher;
		  	
  function setUp()
  {
    $this->request = new Mockrequest($this);
    $this->fetcher = new Mockfetcher($this);
    $this->site_object = new Mocksite_object($this);
    
    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getFetcher', $this->fetcher);
    $this->toolkit->setReturnValue('getRequest', $this->request);
     
    Limb :: registerToolkit($this->toolkit);
    
  	$this->delete_command = new delete_site_object_command();
  }
  
  function tearDown()
  { 
    Limb :: popToolkit();
    
    $this->request->tally();
    $this->fetcher->tally();
  	$this->toolkit->tally();
    $this->site_object->tally();
  }
          
  function test_delete_ok()
  {	
    $object_data = array('class_name' => 'some_class');
  	$this->fetcher->expectOnce('fetch_requested_object', array(new IsAExpectation('Mockrequest')));
  	$this->fetcher->setReturnValue('fetch_requested_object', $object_data);
    $this->toolkit->setReturnValue('createSiteObject', $this->site_object);
    
    $this->site_object->expectOnce('delete'); 
  	$this->assertEqual($this->delete_command->perform(), Limb :: STATUS_OK);
  }
  
  function test_delete_failed()
  {	
    $this->toolkit->setReturnValue('createSiteObject', new site_object_delete_command_test_version1());
    
    $object_data = array('class_name' => 'some_class');
  	$this->fetcher->expectOnce('fetch_requested_object', array(new IsAExpectation('Mockrequest')));
  	$this->fetcher->setReturnValue('fetch_requested_object', $object_data);
    
  	$this->assertEqual($this->delete_command->perform(), Limb :: STATUS_ERROR);
  }

  function test_delete_failed_sql_exception()
  {	
    $this->toolkit->setReturnValue('createSiteObject', new site_object_delete_command_test_version2());
    
    $object_data = array('class_name' => 'some_class');
  	$this->fetcher->expectOnce('fetch_requested_object', array(new IsAExpectation('Mockrequest')));
  	$this->fetcher->setReturnValue('fetch_requested_object', $object_data);
    
    try
    {
      $this->assertEqual($this->delete_command->perform(), Limb :: STATUS_ERROR);
      $this->assertTrue(false);
    }
    catch(SQLException $e)
    {
      $this->assertEqual($e->getMessage(), 'catch me!');
    }
  }
  
}

?>