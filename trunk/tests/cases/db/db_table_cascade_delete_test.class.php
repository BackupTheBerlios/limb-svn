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

require_once(LIMB_DIR . '/class/db/db_table_factory.class.php');
require_once(LIMB_DIR . '/class/db/db_table.class.php');

class test_image_db_table extends db_table
{    
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
class db_table_cascade_delete_test extends db_test
{
	var $image = null;
	var $image_variation = null;
	var $media = null;
	
	var $dump_file = 'cascade_delete.sql'; 
	
	function setUp()
	{
		$this->image =& db_table_factory :: instance('test_image');
		$this->image_variation =& db_table_factory :: instance('test_image_variation');
		$this->media =& db_table_factory :: instance('test_media');
		
		parent :: setUp();
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