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
require_once(LIMB_DIR . '/tests/cases/db_test.class.php');
require_once(LIMB_DIR . 'class/core/access_policy.class.php');

class access_templates_test extends db_test 
{  	
	var $dump_file = 'access_policy_load.sql';

	var $ac = null;
	
  function setUp()
  {
  	parent :: setUp();
  	
  	$this->ac =& access_policy :: instance();
  }
	
	function test_load_user_access_templates()
	{
		$template = $this->ac->get_user_action_access_templates($class_id = 11);
		
		$this->assertEqual(sizeof($template), 2);
		
		$this->assertEqual($template, 
			array(
				'create' => array( 
						200 => array('r' => 1, 'w' => 1),
						210 => array('r' => 1, 'w' => 0),
				),
				'publish' => array( 
						200 => array('r' => 1, 'w' => 0),
						210 => array('r' => 0, 'w' => 1),
				),
			)	
		);
	}

	function test_load_group_access_templates()
	{
		$template = $this->ac->get_group_action_access_templates($class_id = 10);
		
		$this->assertEqual(sizeof($template), 1);
		
		$this->assertEqual($template, 
			array(
				'create' => array( 
						100 => array('r' => 1, 'w' => 1),
						110 => array('r' => 1, 'w' => 0),
				),
			)	
		);

		$template = $this->ac->get_group_action_access_templates($class_id = 11);
		
		$this->assertEqual(sizeof($template), 1);
		
		$this->assertEqual($template, 
			array(
				'create' => array( 
						100 => array('r' => 1, 'w' => 0),
						110 => array('r' => 1, 'w' => 1),
				),
			)	
		);
	}
	
  function test_save_user_actions_access_template()
  {
	 	$template = array(
				'create' => array(
		    		200 => array(
		    				'r' => 1,
		    				'w' => 1,
		    		),
		    		210 => array(
		    				'r' => 0,
		    				'w' => 0,
		    		),
		    ),
				'publish' => array(
		    		200 => array(
		    				'r' => 1,
		    				'w' => 0,
		    		),
		    		210 => array(
		    				'r' => 1,
		    				'w' => 0,
		    		),
		    )
   	);
	
		$this->ac->save_user_action_access_template($class_id = 11, $template);
		
		$db_table	=& db_table_factory :: instance('sys_user_object_access_template');
		$templates_rows = $db_table->get_list('', 'id', null);

		$items_db_table	=& db_table_factory :: instance('sys_user_object_access_template_item');
		$items_rows = $items_db_table->get_list('', 'id', null);

		$this->assertTrue(is_array($templates_rows));
		$this->assertEqual(count($templates_rows), 3);

		$this->assertTrue(is_array($items_rows));
		$this->assertEqual(count($items_rows), 5);
	
		$this->assertEqual($templates_rows, 
			array(
				array('id' => $templates_rows[0]['id'], 'class_id' => 12, 'action_name' => 'create'),
				array('id' => $templates_rows[1]['id'], 'class_id' => 11, 'action_name' => 'create'),
				array('id' => $templates_rows[2]['id'], 'class_id' => 11, 'action_name' => 'publish'),
			)
		);
		
		$record = reset($items_rows);
		$this->assertEqual($record['user_id'], 200); 
		$this->assertEqual($record['r'], 1); 
		$this->assertEqual($record['w'], 0); 
		
		$record = end($items_rows);
		$this->assertEqual($record['user_id'], 210); 
		$this->assertEqual($record['r'], 1); 
		$this->assertEqual($record['w'], 0); 
  }    
}
?>