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

require_once(LIMB_DIR . 'class/lib/db/db_factory.class.php'); 
require_once(LIMB_DIR . 'class/db_tables/db_table_factory.class.php');
require_once(LIMB_DIR . 'class/core/sys_param.class.php');

class sys_params_test extends LimbTestCase 
{
  var $db = null;
  
  function sys_params_test() 
  {
  	$this->db =& db_factory :: instance();
  	parent :: LimbTestCase();
  }
  
  function setUp()
  {
  	debug_mock :: init($this);

  	$this->db->sql_delete('sys_param');
  }

  function tearDown()
  {
  	debug_mock :: tally();
  	
  	$this->db->sql_delete('sys_param');
  }
    
  function test_save_char_value()
  {
  	$sp =& sys_param :: instance();

  	$result = $sp->save_param('param_1', 'char', 123);
  	
  	$this->assertNotNull($result);
  	
  	$db_table =& db_table_factory :: create('sys_param');
  	$list = $db_table->get_list();
  	$this->assertEqual(count($list) , 1);
  	
		$record = current($list);
		
		$this->assertEqual($record['type'], 'char');
		$this->assertEqual($record['char_value'], '123');
		$this->assertNull($record['int_value']);
		$this->assertNull($record['float_value']);
		$this->assertNull($record['blob_value']);
	}

  function test_save_int_value()
  {
  	$sp =& sys_param :: instance();

  	$result = $sp->save_param('param_1', 'int', 123);
  	
  	$this->assertNotNull($result);
  	
  	$db_table =& db_table_factory :: create('sys_param');
  	$list = $db_table->get_list();
  	$this->assertEqual(count($list) , 1);
		$record = current($list);
		
		$this->assertEqual($record['type'], 'int');
		$this->assertEqual($record['int_value'], 123);
		$this->assertNull($record['char_value']);
		$this->assertNull($record['float_value']);
		$this->assertNull($record['blob_value']);
	}
	
  function test_save_float_value()
  {
  	$sp =& sys_param :: instance();

  	$result = $sp->save_param('param_1', 'float', 123.053);
  	
  	$this->assertNotNull($result);
  	
  	$db_table =& db_table_factory :: create('sys_param');
  	$list = $db_table->get_list();
  	$this->assertEqual(count($list) , 1);
		$record = current($list);
		
		$this->assertEqual($record['type'], 'float');
		$this->assertEqual($record['float_value'], 123.053);
		$this->assertNull($record['int_value']);
		$this->assertNull($record['char_value']);
		$this->assertNull($record['blob_value']);
	}
	
  function test_save_blob()
  {
  	$sp =& sys_param :: instance();

  	$result = $sp->save_param('param_1', 'blob', 123.053);
  	
  	$this->assertNotNull($result);
  	
  	$db_table =& db_table_factory :: create('sys_param');
  	$list = $db_table->get_list();
  	$this->assertEqual(count($list) , 1);
		$record = current($list);
		
		$this->assertEqual($record['type'], 'blob');
		$this->assertEqual($record['blob_value'], 123.053);
		$this->assertNull($record['int_value']);
		$this->assertNull($record['char_value']);
		$this->assertNull($record['float_value']);
	}
	
  function test_save_multitype_value()
  {
  	$sp =& sys_param :: instance();

  	$result = $sp->save_param('param_1', 'float', 123.053);
  	$this->assertNotNull($result);
  	$result = $sp->save_param('param_1', 'int', 123.053);
  	
  	$this->assertNotNull($result);
  	
  	$db_table =& db_table_factory :: create('sys_param');
  	$list = $db_table->get_list();
  	$this->assertEqual(count($list) , 1);
		$record = current($list);
		
		$this->assertEqual($record['type'], 'int');
		$this->assertEqual($record['int_value'], 123);
		$this->assertEqual($record['float_value'],0);
		$this->assertEqual($record['char_value'],'');
		$this->assertEqual($record['blob_value'],'');

  	$result = $sp->save_param('param_1', 'char', 123.053, false);
  	
  	$this->assertNotNull($result);
  	
  	$list = $db_table->get_list();
  	$this->assertEqual(count($list) , 1);
		$record = current($list);
		
		$this->assertEqual($record['type'], 'char');
		$this->assertEqual($record['char_value'],'123.053');
		$this->assertEqual($record['float_value'],0);
		$this->assertEqual($record['int_value'], 123);
		$this->assertEqual($record['blob_value'],'');
	}

  function test_save_wrong_type_value()
  {
  	$sp =& sys_param :: instance();
				
		try
		{
  	  $result = $sp->save_param('param_1', 'sadnkfjhskjfd', 123.053);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'trying to save undefined type in sys_param');  	  
  	  $this->assertEqual($e->getAdditionalParams(),
    	  array (
  			  'type' => 'sadnkfjhskjfd',
  			  'param' => 'param_1',
  			)
  		);
  	}
	}		

  function test_get_value()
  {
  	$sp =& sys_param :: instance();

		$number = 123.053;
  	$sp->save_param('param_1', 'float', $number);
  	
  	$this->assertEqual($sp->get_param('param_1'), $number);
  	$this->assertNull($sp->get_param('param_1', 'char'));
	}		

  function test_get_wrong_type_value()
  {
  	$sp =& sys_param :: instance();

		$number = 123.053;
		$sp->save_param('param_1', 'float', $number);
		
		try
		{
  	  $this->assertNull($sp->get_param('param_1', 'blabla'));  
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'trying to get undefined type in sys_param');  	  
  	  $this->assertEqual($e->getAdditionalParams(),
    	  array (
  			  'type' => 'blabla',
  			  'param' => 'param_1',
  			)
  		);  	
  	}
	}		
}
?>