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
require_once(LIMB_DIR . '/class/core/finders/versioned_one_table_objects_raw_finder.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_table.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('db_table');

class test_versioned_one_table_objects_raw_finder extends versioned_one_table_objects_raw_finder
{
  protected function _define_db_table_name()
  {
    return 'table1';
  }
}

Mock :: generatePartial('test_versioned_one_table_objects_raw_finder',
                        'versioned_one_table_objects_raw_finder_test_version',
                        array('_do_parent_find',
                              '_do_parent_find_count'));

class versioned_one_table_objects_raw_finder_test extends LimbTestCase
{
	var $finder;
	var $toolkit;
	var $db_table;
  
  function setUp()
  {
    $this->finder = new versioned_one_table_objects_raw_finder_test_version($this);
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

    $expected_sql_params = $sql_params;
    $expected_sql_params['conditions'][] = ' AND sso.current_version=tn.version';
    
    $this->finder->expectOnce('_do_parent_find', array(new EqualExpectation($params), 
                                                        new EqualExpectation($expected_sql_params)));
    $this->finder->setReturnValue('_do_parent_find', $result = 'some result');
     
    $this->assertEqual($this->finder->find($params, $sql_params), $result);
  }

  function test_find_by_version()
  {
    $expected_sql_params = array();
    $expected_sql_params['conditions'][] = ' AND sso.id=' . $object_id = 100;
    $expected_sql_params['conditions'][] = ' AND tn.version=' . $version = 1000;
    
    $this->finder->expectOnce('_do_parent_find', array(array(), 
                                                        new EqualExpectation($expected_sql_params)));
    $this->finder->setReturnValue('_do_parent_find', $result = 'some result');
     
    $this->assertEqual($this->finder->find_by_version($object_id, $version), $result);
  }
  
  function test_find_count()
  {
    $sql_params['conditions'][] = 'some condition';
    $expected_sql_params = $sql_params;
    
    $expected_sql_params['conditions'][] = ' AND sso.current_version=tn.version';
    
    $this->finder->expectOnce('_do_parent_find_count', 
                              array(new EqualExpectation($expected_sql_params)));
    
    $this->finder->setReturnValue('_do_parent_find_count', $result = 'some result');
     
    $this->assertEqual($this->finder->find_count($sql_params), $result);
  }
  
}

?>