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
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(LIMB_DIR . '/class/db_tables/DbTableFactory.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbModule.class.php');
require_once(LIMB_DIR . '/class/finders/SiteObjectsRawFinder.class.php');
require_once(LIMB_DIR . '/class/behaviours/SiteObjectBehaviour.class.php');
require_once(LIMB_DIR . '/class/tree/MaterializedPathTree.class.php');

Mock :: generate('DbModule');
Mock :: generate('LimbToolkit');

Mock :: generatePartial('SiteObjectsRawFinder',
                        'SiteObjectsRawFinderFindVersionMock',
                        array());

class SiteObjectsRawFinderFindTestVersion extends SiteObjectsRawFinderFindVersionMock
{
  var $_mocked_methods = array('find');

  function find($params = array(), $sql_params = array())
  {
    $args = func_get_args();
    return $this->_mock->_invoke('find', $args);
  }
}

class SiteObjectsRawFinderTest extends LimbTestCase
{
  var $class_id;
  var $finder;
  var $db;
  var $root_node_id;
  var	$behaviour_id;

  function setUp()
  {
    $this->db =& DbFactory :: instance();

    $this->_cleanUp();

    $this->_insertSysClassRecord();
    $this->_insertSysBehaviourRecord();

    $this->_insertSysSiteObjectRecords();
    $this->_insertFakeSysSiteObjectRecords();

    $this->finder = new SiteObjectsRawFinder();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('sys_site_object');
    $this->db->sqlDelete('sys_site_object_tree');
    $this->db->sqlDelete('sys_class');
    $this->db->sqlDelete('sys_behaviour');
  }

  function testFindRequiredFields()
  {
    $sql_params['conditions'][] = ' AND sso.id = 1';
    $objects_data = $this->finder->find(array(), $sql_params);
    $record = reset($objects_data);

    $this->assertEqual($record['identifier'], 'object_1');
    $this->assertEqual($record['title'], 'object_1_title');
    $this->assertTrue(array_key_exists('current_version', $record));
    $this->assertTrue(array_key_exists('modified_date', $record));
    $this->assertEqual($record['status'], 0);
    $this->assertTrue(array_key_exists('created_date', $record));
    $this->assertTrue(array_key_exists('creator_id', $record));
    $this->assertEqual($record['locale_id'], 'en');
    $this->assertTrue(array_key_exists('id', $record));
    $this->assertTrue(array_key_exists('node_id', $record));
    $this->assertEqual($record['parent_node_id'], $this->root_node_id);
    $this->assertTrue(array_key_exists('level', $record));
    $this->assertTrue(array_key_exists('priority', $record));
    $this->assertTrue(array_key_exists('children', $record));
    $this->assertEqual($record['class_id'], $this->class_id);
    $this->assertEqual($record['class_name'], 'site_object');
    $this->assertTrue(array_key_exists('behaviour_id', $record));
    $this->assertEqual($record['behaviour'], 'site_object_behaviour');
    $this->assertTrue(array_key_exists('icon', $record));
    $this->assertTrue(array_key_exists('sort_order', $record));
    $this->assertTrue(array_key_exists('can_be_parent', $record));
  }

  function testFindSql()
  {
    $db_mock = new MockDbModule($this);
    $toolkit = new MockLimbToolkit($this);

    $toolkit->setReturnReference('getDB', $db_mock);

    Limb :: registerToolkit($toolkit);

    $params = array();
    $sql_params = array();

    $params['order'] = array('col1' => 'DESC', 'col2' => 'ASC');
    $params['limit'] = 10;
    $params['offset'] = 5;

    $sql_params['columns'][] = 'test-column1,';
    $sql_params['columns'][] = 'test-column2,';

    $sql_params['tables'][] = ',test-table1';
    $sql_params['tables'][] = ',test-table2';

    $sql_params['conditions'][] = 'OR test-condition1';
    $sql_params['conditions'][] = 'AND test-condition2';

    $sql_params['group'][] = 'GROUP BY test-group';

    $expectation = new WantedPatternExpectation(
    "~^SELECT.*sso\.locale_id as locale_id,.*test-column1, test-column2,.*sso\.title as title,.* sys_site_object_tree as ssot.*,test-table1 ,test-table2.*WHERE sys_class\.id = sso\.class_id.*AND ssot\.object_id = sso\.id.*OR test-condition1 AND test-condition2 GROUP BY test-group ORDER BY col1 DESC, col2 ASC$~s");

    $db_mock->expectOnce('sqlExec', array($expectation, 10, 5));
    $db_mock->expectOnce('getArray', array('id'));

    $this->finder->find($params, $sql_params);

    Limb :: popToolkit();

    $db_mock->tally();
  }

  function testFindCountSqlWithGroup()
  {
    $db_mock = new MockDbModule($this);
    $toolkit = new MockLimbToolkit($this);

    $toolkit->setReturnReference('getDB', $db_mock);

    Limb :: registerToolkit($toolkit);

    $sql_params['tables'][] = ',table1';
    $sql_params['tables'][] = ',table2';

    $sql_params['conditions'][] = 'OR cond1';
    $sql_params['conditions'][] = 'AND cond2';

    $sql_params['group'][] = 'GROUP BY test-group';

    $expectation = new WantedPatternExpectation(
    "~^SELECT COUNT\(sso\.id\) as count.*FROM sys_site_object as sso ,table1 ,table2.*WHERE sso\.id OR cond1 AND cond2 GROUP BY test-group~s");

    $db_mock->expectOnce('sqlExec', array($expectation));
    $db_mock->expectOnce('countSelectedRows');
    $db_mock->setReturnValue('countSelectedRows', $result = 10);

    $this->assertEqual($result, $this->finder->findCount($sql_params));

    Limb :: popToolkit();

    $db_mock->tally();
  }

  function testFindCountSqlNoGroup()
  {
    $db_mock = new MockDbModule($this);
    $toolkit = new MockLimbToolkit($this);

    $toolkit->setReturnReference('getDB', $db_mock);

    Limb :: registerToolkit($toolkit);

    $sql_params['tables'][] = ',table1';
    $sql_params['tables'][] = ',table2';

    $sql_params['conditions'][] = 'OR cond1';
    $sql_params['conditions'][] = 'AND cond2';

    $expectation = new WantedPatternExpectation(
    "~^SELECT COUNT\(sso\.id\) as count.*FROM sys_site_object as sso ,table1 ,table2.*WHERE sso\.id OR cond1 AND cond2~s");

    $db_mock->expectOnce('sqlExec', array($expectation));
    $db_mock->expectNever('countSelectedRows');
    $db_mock->expectOnce('fetchRow');
    $db_mock->setReturnValue('fetchRow', array('count' => 10));

    $this->assertEqual(10, $this->finder->findCount($sql_params));

    Limb :: popToolkit();

    $db_mock->tally();
  }

  function testFindById()
  {
    $finder = new SiteObjectsRawFinderFindTestVersion($this);
    $finder->expectOnce('find', array(array(),
                                      array('conditions' => array(' AND sso.id='. $id = 100))));

    $finder->expectOnce('find');
    $finder->findById($id);

    $finder->tally();
  }

  function testFindNoParams()
  {
    $result = $this->finder->find();

    for($i = 1; $i <=5; $i++)
    {
      $this->assertEqual($result[$i]['identifier'], 'object_' . $i);
      $this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
    }
  }

  function testFindLimitOffset()
  {
    $params['limit'] = $limit = 3;
    $params['offset'] = $limit = 2;
    $result = $this->finder->find($params);

    for($i = 3; $i <= 5; $i++)
    {
      $this->assertEqual($result[$i]['identifier'], 'object_' . $i);
      $this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
    }
  }

  function testFindLimitOffsetOrder()
  {
    $params['limit'] = $limit = 3;
    $params['offset'] = 2;
    $params['order'] = array('title' => 'DESC');
    $result = $this->finder->find($params);

    for($i = 7; $i >=5; $i--)
    {
      $this->assertEqual($result[$i]['identifier'], 'object_' . $i);
      $this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
    }
  }

  function testCount()
  {
    $result = $this->finder->findCount();
    $this->assertEqual($result, 10);
  }

  function _insertSysClassRecord()
  {
    $db_table = DbTableFactory :: create('SysClass');
    $db_table->insert(array('name' => 'site_object'));

    $this->class_id = $db_table->getLastInsertId();
  }

  function _insertSysBehaviourRecord()
  {
    $db_table = DbTableFactory :: create('SysBehaviour');
    $db_table->insert(array('name' => 'site_object_behaviour'));

    $this->behaviour_id = $db_table->getLastInsertId();
  }

  function _insertSysSiteObjectRecords()
  {
    $tree = new MaterializedPathTree();

    $values['identifier'] = 'root';
    $this->root_node_id = $tree->createRootNode($values, false, true);

    $data = array();
    for($i = 1; $i <= 5; $i++)
    {
      $version = mt_rand(1, 3);

      $this->db->sqlInsert('sys_site_object',
        array(
          'id' => $i,
          'class_id' => $this->class_id,
          'behaviour_id' => $this->behaviour_id,
          'current_version' => $version,
          'identifier' => 'object_' . $i,
          'title' => 'object_' . $i . '_title',
          'status' => 0,
          'locale_id' => 'en',
        )
      );

      $values['identifier'] = 'object_' . $i;
      $values['object_id'] = $i;
      $tree->createSubNode($this->root_node_id, $values);
    }
  }

  function _insertFakeSysSiteObjectRecords()
  {
    $class_db_table = DbTableFactory :: create('SysClass');
    $class_db_table->insert(array('id' => 1001, 'class_name' => 'fake_class'));

    $tree = new MaterializedPathTree();

    $db_table =& DbTableFactory :: create('SysSiteObject');

    $data = array();
    for($i = 6; $i <= 10 ; $i++)
    {
      $this->db->sqlInsert('sys_site_object',
        array(
          'id' => $i,
          'class_id' => 1001,
          'behaviour_id' => $this->behaviour_id,
          'identifier' => 'object_' . $i,
          'title' => 'object_' . $i . '_title',
          'status' => 0,
          'locale_id' => 'en',
        )
      );

      $values['identifier'] = 'object_' . $i;
      $values['object_id'] = $i;
      $tree->createSubNode($this->root_node_id, $values);
    }
  }
}

?>