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
require_once(LIMB_DIR . '/class/core/finders/one_table_objects_raw_finder.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_table.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('db_table');

class test_one_table_objects_raw_finder extends one_table_objects_raw_finder
{
  protected function _define_db_table_name()
  {
    return 'table1';
  }
}

Mock :: generatePartial('test_one_table_objects_raw_finder',
                        'one_table_objects_raw_finder_test_version',
                        array('_do_parent_find',
                              '_do_parent_find_count'));

class one_table_objects_raw_finder_test extends LimbTestCase
{
	var $finder;
	var $toolkit;
	var $db_table;
  
  function setUp()
  {
    $this->finder = new one_table_objects_raw_finder_test_version($this);
    $this->db_table = new Mockdb_table($this);
    $this->toolkit = new MockLimbToolkit($this);
    
    $this->toolkit->setReturnValue('createDBTable', $this->db_table);
        
    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->db_table->tally();
    $this->finder->tally();
    
    Limb :: popToolkit();
  }

  function test_find()
  {
    $params['limit'] = 5;
    $sql_params['conditions'][] = 'some condition';
    
    $this->db_table->expectOnce('get_columns_for_select', array('tn', array('id')));
    $this->db_table->setReturnValue('get_columns_for_select', 'tn.field1, tn.field2');
    $this->db_table->expectOnce('get_table_name');
    $this->db_table->setReturnValue('get_table_name', 'table1');
    
    $expected_sql_params = $sql_params;
    $expected_sql_params['columns'][] = ' tn.field1, tn.field2,';
    $expected_sql_params['tables'][] = ',table1 as tn';
    $expected_sql_params['conditions'][] = 'AND sso.id=tn.object_id';
    
    $this->finder->expectOnce('_do_parent_find', array(new EqualExpectation($params), 
                                                        new EqualExpectation($expected_sql_params)));
    $this->finder->setReturnValue('_do_parent_find', $result = 'some result');
     
    $this->assertEqual($this->finder->find($params, $sql_params), $result);
  }
  
  function test_find_by_id()
  {
    $this->db_table->expectOnce('get_columns_for_select', array('tn', array('id')));
    $this->db_table->setReturnValue('get_columns_for_select', 'tn.field1, tn.field2');
    $this->db_table->expectOnce('get_table_name');
    $this->db_table->setReturnValue('get_table_name', 'table1');
    
    $expected_sql_params = array();
    $expected_sql_params['conditions'][] = ' AND sso.id='. $id = 100;
    $expected_sql_params['conditions'][] = 'AND sso.id=tn.object_id';
    $expected_sql_params['columns'][] = ' tn.field1, tn.field2,';
    $expected_sql_params['tables'][] = ',table1 as tn';
    
    $this->finder->expectOnce('_do_parent_find', array(array(), 
                                                        new EqualExpectation($expected_sql_params)));
    $this->finder->setReturnValue('_do_parent_find', $result = 'some result');

    $this->assertEqual($this->finder->find_by_id($id), $result);
  }

  function test_find_count()
  {
    $sql_params['conditions'][] = 'some condition';
    
    $this->db_table->expectOnce('get_table_name');
    $this->db_table->setReturnValue('get_table_name', 'table1');
    
    $expected_sql_params = $sql_params;
    $expected_sql_params['tables'][] = ',table1 as tn';
    $expected_sql_params['conditions'][] = 'AND sso.id=tn.object_id';
    
    $this->finder->expectOnce('_do_parent_find_count', 
                              array(new EqualExpectation($expected_sql_params)));
    
    $this->finder->setReturnValue('_do_parent_find_count', $result = 'some result');
     
    $this->assertEqual($this->finder->find_count($sql_params), $result);
  }
}

?>