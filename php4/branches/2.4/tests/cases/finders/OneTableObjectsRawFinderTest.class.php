<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/finders/OneTableObjectsRawFinder.class.php');
require_once(LIMB_DIR . '/class/lib/db/LimbDbTable.class.php');
require_once(LIMB_DIR . '/class/LimbToolkit.interface.php');

Mock :: generate('LimbToolkit');
Mock :: generate('LimbDbTable');

class TestOneTableObjectsRawFinder extends OneTableObjectsRawFinder
{
  function _defineDbTableName()
  {
    return 'table1';
  }
}

Mock :: generatePartial('TestOneTableObjectsRawFinder',
                        'OneTableObjectsRawFinderTestVersion',
                        array('_doParentFind',
                              '_doParentFindCount'));

class OneTableObjectsRawFinderTest extends LimbTestCase
{
  var $finder;
  var $toolkit;
  var $db_table;

  function setUp()
  {
    $this->finder = new OneTableObjectsRawFinderTestVersion($this);
    $this->db_table = new MockDbTable($this);
    $this->toolkit = new MockLimbToolkit($this);

    $this->toolkit->setReturnReference('createDBTable', $this->db_table);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->toolkit->tally();
    $this->db_table->tally();
    $this->finder->tally();

    Limb :: popToolkit();
  }

  function testFind()
  {
    $params['limit'] = 5;
    $sql_params['conditions'][] = 'some condition';

    $this->db_table->expectOnce('getColumnsForSelect', array('tn', array('id')));
    $this->db_table->setReturnValue('getColumnsForSelect', 'tn.field1, tn.field2');
    $this->db_table->expectOnce('getTableName');
    $this->db_table->setReturnValue('getTableName', 'table1');

    $expected_sql_params = $sql_params;
    $expected_sql_params['columns'][] = ' tn.field1, tn.field2,';
    $expected_sql_params['tables'][] = ',table1 as tn';
    $expected_sql_params['conditions'][] = 'AND sso.id=tn.object_id';

    $this->finder->expectOnce('_doParentFind', array(new EqualExpectation($params),
                                                        new EqualExpectation($expected_sql_params)));
    $this->finder->setReturnValue('_doParentFind', $result = 'some result');

    $this->assertEqual($this->finder->find($params, $sql_params), $result);
  }

  function testFindById()
  {
    $this->db_table->expectOnce('getColumnsForSelect', array('tn', array('id')));
    $this->db_table->setReturnValue('getColumnsForSelect', 'tn.field1, tn.field2');
    $this->db_table->expectOnce('getTableName');
    $this->db_table->setReturnValue('getTableName', 'table1');

    $expected_sql_params = array();
    $expected_sql_params['conditions'][] = ' AND sso.id='. $id = 100;
    $expected_sql_params['conditions'][] = 'AND sso.id=tn.object_id';
    $expected_sql_params['columns'][] = ' tn.field1, tn.field2,';
    $expected_sql_params['tables'][] = ',table1 as tn';

    $this->finder->expectOnce('_doParentFind', array(array(),
                                                        new EqualExpectation($expected_sql_params)));
    $this->finder->setReturnValue('_doParentFind', $result = 'some result');

    $this->assertEqual($this->finder->findById($id), $result);
  }

  function testFindCount()
  {
    $sql_params['conditions'][] = 'some condition';

    $this->db_table->expectOnce('getTableName');
    $this->db_table->setReturnValue('getTableName', 'table1');

    $expected_sql_params = $sql_params;
    $expected_sql_params['tables'][] = ',table1 as tn';
    $expected_sql_params['conditions'][] = 'AND sso.id=tn.object_id';

    $this->finder->expectOnce('_doParentFindCount',
                              array(new EqualExpectation($expected_sql_params)));

    $this->finder->setReturnValue('_doParentFindCount', $result = 'some result');

    $this->assertEqual($this->finder->findCount($sql_params), $result);
  }
}

?>