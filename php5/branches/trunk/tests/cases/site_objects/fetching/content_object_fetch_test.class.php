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
require_once(LIMB_DIR . '/class/core/site_objects/content_object.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_table.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('db_table');

Mock :: generatePartial('content_object',
                        'content_object_fetch_test_version',
                        array('_do_parent_fetch',
                              '_do_parent_fetch_count'));

class content_object_fetch_test extends LimbTestCase
{
	var $object;
	var $toolkit;
	var $db_table;
  
  function setUp()
  {
    $this->object = new content_object_fetch_test_version($this);
    $this->db_table = new Mockdb_table($this);
    $this->toolkit = new MockLimbToolkit($this);
    
    $this->toolkit->setReturnValue('createDBTable', $this->db_table);
        
    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->db_table->tally();
    $this->object->tally();
    
    Limb :: popToolkit();
  }

  function test_fetch()
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
    $expected_sql_params['conditions'][] = 'AND sso.id=tn.object_id AND sso.current_version=tn.version';
    
    $this->object->expectOnce('_do_parent_fetch', array(new EqualExpectation($params), 
                                                        new EqualExpectation($expected_sql_params)));
    $this->object->setReturnValue('_do_parent_fetch', $result = 'some result');
     
    $this->assertEqual($this->object->fetch($params, $sql_params), $result);
  }

  function test_fetch_count()
  {
    $sql_params['conditions'][] = 'some condition';
    
    $this->db_table->expectOnce('get_table_name');
    $this->db_table->setReturnValue('get_table_name', 'table1');
    
    $expected_sql_params = $sql_params;
    $expected_sql_params['tables'][] = ',table1 as tn';
    $expected_sql_params['conditions'][] = 'AND sso.id=tn.object_id AND sso.current_version=tn.version';
    
    $this->object->expectOnce('_do_parent_fetch_count', 
                              array(new EqualExpectation($expected_sql_params)));
    
    $this->object->setReturnValue('_do_parent_fetch_count', $result = 'some result');
     
    $this->assertEqual($this->object->fetch_count($sql_params), $result);
  }
  
}

?>