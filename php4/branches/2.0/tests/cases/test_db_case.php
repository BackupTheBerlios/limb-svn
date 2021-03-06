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
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

class test_db_case extends UnitTestCase 
{   	 	
	var $db = null;
	
	var $dump_file = '';

	var $tables_list = array();
	var $table_records = array();
	
	var $sql_array = array();
	
  function test_db_case() 
  {
  	$this->db =& db_factory :: instance();
  	
  	parent :: UnitTestCase();
  }
  
  function _clean_up()
  {
  	foreach($this->tables_list as $table)
  		$this->db->sql_delete($table);
  }
  
  function _load_tables_list()
  {
  	if(!$this->dump_file)
  		return;

  	$this->sql_array = file(TEST_CASES_DIR . $this->dump_file);
  	
  	$tables = array();
  	
  	foreach($this->sql_array as $sql)
  	{
  		if(preg_match("|insert\s+?into\s+?([^\s]+)|i", $sql, $matches))
  		{
  			if(!isset($tables[$matches[1]]))
  			{
  				$this->tables_list[] = $matches[1];	
  				
  				if(!isset($this->table_records[$matches[1]]))
  					$this->table_records[$matches[1]] = 0;
  					
  				$this->table_records[$matches[1]]++;
  					
  			}
  		}
		}
  }
  
  function _load_dumped_db()
  {
  	if(!$this->dump_file)
  		return;

  	$this->sql_array = file(TEST_CASES_DIR . $this->dump_file);
  	
  	foreach($this->sql_array as $sql)
  	{    		
  		$this->db->sql_exec($sql);
  	}
  }
      
  function setUp()
  {
  	debug_mock :: init($this);
  	
  	$this->_load_tables_list();	
  	
  	$this->_clean_up();	
  	
  	$this->_load_dumped_db();
  }
  
  function tearDown()
  {
  	debug_mock :: tally();
  	
  	$this->_clean_up();    	
  }    
  
}
?>