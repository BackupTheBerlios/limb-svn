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
require_once(LIMB_DIR . 'core/lib/system/dir.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/model/site_objects/poll_container.class.php');

Mock::generatePartial
(
  'poll_container',
  'poll_container_test_version',
  array('get_active_poll')
); 

class test_poll_container extends UnitTestCase 
{  	
	var $db = null;
	
	var $obj = null;

  function test_poll_container() 
  {
  	parent :: UnitTestCase();
		
 		$this->db = db_factory :: instance();  	
  }
  
	function setUp()
	{
   $this->obj =& new poll_container_test_version($this);
   $this->obj->poll_container();
	} 
	
	function tearDown()
	{
		$this->obj->tally();
		unset($this->obj);
	} 
    
  function test_can_vote()
  {
		//$this->obj->expectOnce('get_active_poll');
		//$this->assertTrue($this->obj->can_vote());									
	}
}

?>