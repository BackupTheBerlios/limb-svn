<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/core/model/site_objects/composite_user_object.class.php');


class test_composite_user_manipulation extends UnitTestCase 
{ 
	var $node_object_mocks = array();
	 	
  function test_composite_user_manipulation() 
  {
  	parent :: UnitTestCase();
  }

  function & _get_node_object_mocks()
  {
  	return array();
  }
  
  function & _get_composite_object()
  {
  	return new composite_user_object();
  }
  
  function _get_methods_array()
  {
  	return array(
  								'create', 
  								'update', 
  								'delete', 
  								'login', 
  								'logout', 
  								'activate_password', 
  								'generate_password',
  								'change_password',
  								'change_own_password',
  								'import_attributes'
  								);
  }
  
  function setUp()
  {
  	$this->composite_object = $this->_get_composite_object();
  	
		$this->node_object_mocks =& $this->_get_node_object_mocks();
		
  	foreach(array_keys($this->node_object_mocks) as $id)
  		$this->composite_object->_register_node_object($this->node_object_mocks[$id]);

  }
  
  function tearDown()
  {
  	parent :: tearDown();
  	
  	foreach(array_keys($this->node_object_mocks) as $id )
  		$this->node_object_mocks[$id]->tally();
  }
  
  function test_walk_node_objects()
  {
  	foreach($this->_get_methods_array() as $method)
  	{
	  	foreach(array_keys($this->node_object_mocks) as $id)
	  	{
	  		$this->node_object_mocks[$id]->expectOnce($method);
	  		$this->node_object_mocks[$id]->setReturnValue($method, true);
	  	}
			
			$this->composite_object->_walk_node_objects($method);
  	}
  }

}

?>