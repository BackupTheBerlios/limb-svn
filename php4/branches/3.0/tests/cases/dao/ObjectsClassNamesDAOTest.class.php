<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: TreeBranchCriteriaTest.class.php 1173 2005-03-17 11:36:43Z seregalimb $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/UnitOfWork.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/dao/ObjectsClassNamesDAO.class.php');
require_once(LIMB_DIR . '/core/dao/criteria/SimpleConditionCriteria.class.php');

Mock :: generate('UnitOfWork');
Mock :: generate('Request');

class ObjectsClassNamesDAOTest extends LimbTestCase
{
  var $dao;
  var $uow;
  var $request;
  var $conn;
  var $db;

  function ObjectsClassNamesDAOTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit = Limb :: toolkit();
    $this->conn =& $toolkit->getDBConnection();
    $this->db = new SimpleDB($this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_object');
    $this->db->delete('sys_class');
  }

  function testFetchById()
  {
    $this->db->insert('sys_object', array('oid' => $id = 10,
                                          'class_id' => $class_id = 50));

    $this->db->insert('sys_class', array('id' => $class_id,
                                         'name' => $class_name = 'TestArticle'));

    $dao = new ObjectsClassNamesDAO();
    $record = $dao->fetchById($id);
    $this->assertEqual($record->get('oid'), $id);
    $this->assertEqual($record->get('name'), $class_name);
  }

  function testFetch()
  {
    $this->db->insert('sys_object', array('oid' => $id1 = 10,
                                          'class_id' => $class_id1 = 50));

    $this->db->insert('sys_class', array('id' => $class_id1,
                                         'name' => $class_name1 = 'TestArticle'));

    $this->db->insert('sys_object', array('oid' => $id2 = 11,
                                          'class_id' => $class_id2 = 51));

    $this->db->insert('sys_class', array('id' => $class_id2,
                                         'name' => $class_name2 = 'TestDocument'));

    $dao = new ObjectsClassNamesDAO();
    $ids_arr = array($id1, $id2);
    $ids = implode(',', $ids_arr);
    $dao->addCriteria(new SimpleConditionCriteria("sys_object.oid IN ($ids)"));

    $rs = new SimpleDbDataset($dao->fetch());
    $arr = $rs->getArray('oid');
    $this->assertEqual($arr[$id1]['name'], $class_name1);
    $this->assertEqual($arr[$id2]['name'], $class_name2);
  }
}
?>
