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
require_once(LIMB_DIR . '/class/core/commands/edit_site_object_command.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/fetcher.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');

Mock::generate('LimbToolkit');
Mock::generate('request');
Mock::generate('fetcher');
Mock::generate('dataspace');
Mock::generate('site_object');

//do you miss namespaces? yeah, we too :)
class site_object_for_edit_site_object_command extends site_object
{
  public function update($force_create_new_version = true)
  {
    throw new LimbException('catch me!');
  }
}

class edit_site_object_command_test extends LimbTestCase 
{
	var $command;
  var $request;
  var $toolkit;
  var $fetcher;
  var $dataspace;
  var $site_object;
		  	
  function setUp()
  {
    $this->request = new Mockrequest($this);
    $this->fetcher = new Mockfetcher($this);
    $this->dataspace = new Mockdataspace($this);
    $this->site_object = new Mocksite_object($this);
    
    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getFetcher', $this->fetcher);
    $this->toolkit->setReturnValue('getRequest', $this->request);
    $this->toolkit->setReturnValue('getDataspace', $this->dataspace);
    
    $this->toolkit->expectOnce('createSiteObject', array('site_object'));
     
    Limb :: registerToolkit($this->toolkit);
    
  	$this->command = new edit_site_object_command();
  }
  
  function tearDown()
  { 
    Limb :: popToolkit();
    
    $this->request->tally();
    $this->fetcher->tally();
  	$this->toolkit->tally();
    $this->dataspace->tally();
    $this->site_object->tally();
  }

  function test_perform_failure()
  {	
    $this->toolkit->setReturnValue('createSiteObject', 
                                   new site_object_for_edit_site_object_command(),
                                   array('site_object'));
    
    $this->assertEqual(Limb :: STATUS_ERROR, $this->command->perform());
  }

  function test_perform_ok_no_version_increase()
  {	
    $this->dataspace->expectOnce('export');
    $this->dataspace->setReturnValue('export', $data = array('identifier' => 'test',
                                                     'title' => 'Test',
                                                     ));
    
    $this->fetcher->expectOnce('fetch_requested_object');
    $this->fetcher->setReturnValue('fetch_requested_object',
                                   array('node_id' => 100,
                                         'some_other_attrib' => 'some_value'));
    
    $this->site_object->expectOnce('merge', array($data));
    
    $this->site_object->expectOnce('update', array(false));

    $this->toolkit->setReturnValue('createSiteObject', $this->site_object, array('site_object'));
    
    $this->assertEqual(Limb :: STATUS_OK, $this->command->perform());
  }
}

?>