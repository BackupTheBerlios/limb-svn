<?php

	require_once(LIMB_DIR . 'core/model/site_object_factory.class.php');
	require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

  class test_save_metadata extends UnitTestCase 
  {
  	var $db = null;
  
    function test_save_metadata() 
    {
    	parent :: UnitTestCase();
    	
    	$this->db = db_factory :: instance();

    }
    
    function setUp()
    {
    	$this->db->sql_delete('sys_metadata');

    }
    
    function tearDown()
    {
    	$this->db->sql_delete('sys_metadata');
    }
    
    
    function test_save()
    {
    	$metadata['id'] = 1;
    	$metadata['keywords'] = 'keywords';
    	$metadata['description'] = 'description';
    	
    	$o =& site_object_factory :: create('content_object');
    	
    	//trigger_error("Stop",E_USER_WARNING);	
    	$o->import_attributes($metadata);
    	$result_id = $o->save_metadata();
    	
    	$this->assertNotNull($result_id);
    	
    	$sys_metadata_db_table =& db_table_factory :: instance('sys_metadata');
    	$metadata_row = $sys_metadata_db_table->get_row_by_id($result_id);
    	
    	$this->assertTrue(is_array($metadata_row));
    	$this->assertTrue(isset($metadata_row['object_id']));
    	$this->assertTrue(isset($metadata_row['keywords']));
    	$this->assertTrue(isset($metadata_row['description']));

    	$this->assertEqual($metadata_row['object_id'], 1);
    	$this->assertEqual($metadata_row['keywords'], 'keywords');
    	$this->assertEqual($metadata_row['description'], 'description');
		}
  }
?>