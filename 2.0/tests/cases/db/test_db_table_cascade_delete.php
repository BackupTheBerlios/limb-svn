<?php

require_once(TEST_CASES_DIR . 'test_db_case.php');

require_once(LIMB_DIR . '/core/lib/db/db_table_factory.class.php');
require_once(LIMB_DIR . '/core/lib/db/db_table.class.php');

class test_image_db_table extends db_table
{
  function test_image_db_table()
  {
    parent :: db_table();
  }
    
  function _define_columns()
  {
  	return array(
  		'id' => array('type' => 'numeric'),
      'description' => '',
      'title' => '',
    );
  }
  
  function _define_constraints()
  {
    return array(
    	'id' =>	array(
	    		0 => array(
						'table_name' => 'test_image_variation',
						'field' => 'image_id',
					),
			),
    );   
  }
}


class test_image_variation_db_table extends db_table
{
  function test_image_variation_db_table()
  {
    parent :: db_table();
  }
    
  function _define_columns()
  {
  	return array(
  		'id' => array('type' => 'numeric'),
      'image_id' => array('type' => 'numeric'),
      'media_id' => '',
      'width' => '',
      'height' => '',
      'variation' => ''
    );
  }
  
  function _define_constraints()
  {
    return array(
    	'media_id' =>	array(
	    		0 => array(
						'table_name' => 'test_media',
						'field' => 'id',
					),
			),
    );   
  }
}

class test_media_db_table extends db_table
{
  function test_media_db_table()
  {
    parent :: db_table();
  }
    
  function _define_columns()
  {
  	return array(
  		'id' => '',
      'file_name' => '',
      'mime_type' => '',
      'size' => '',
      'etag' => '',
    );
  }  
}
class test_db_table_cascade_delete extends test_db_case
{
	var $image = null;
	var $image_variation = null;
	var $media = null;
	
	var $dump_file = 'cascade_delete.sql'; 

	function test_db_table_cascade_delete($name = 'db table test case')
	{
		$this->image =& db_table_factory :: instance('test_image');
		$this->image_variation =& db_table_factory :: instance('test_image_variation');
		$this->media =& db_table_factory :: instance('test_media');
		
		parent :: test_db_case($name);
	} 

	function test_cascade_delete()
	{
		$this->image_variation->delete(array('id' => 16));
		
		$this->assertEqual(sizeof($this->image_variation->get_list()), 11);
		$this->assertEqual(sizeof($this->media->get_list()), 11);
	}
	
	function test_nested_cascade_delete()
	{
		$this->image->delete(array('id' => 12));
		
		$this->assertEqual(sizeof($this->image->get_list()), 4);
		$this->assertEqual(sizeof($this->image_variation->get_list()), 9);
		$this->assertEqual(sizeof($this->media->get_list()), 9);
	}
			
} 
?>