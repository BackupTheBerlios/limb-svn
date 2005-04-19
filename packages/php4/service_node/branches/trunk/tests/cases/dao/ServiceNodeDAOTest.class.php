<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ImageObjectsDAOTest.class.php 1093 2005-02-07 15:17:20Z pachanga $
*
***********************************************************************************/
require_once(LIMB_SERVICE_NODE_DIR . '/dao/ServiceNodeDAO.class.php');
require_once(LIMB_DIR . '/core/util/Ini.class.php');
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');
require_once(LIMB_DIR . '/core/dao/criteria/SimpleConditionCriteria.class.php');

class ServiceNodeDAOTest extends LimbTestCase
{
  var $db;

  function ServiceNodeDAOTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $this->db =& new SimpleDB($toolkit->getDbConnection());

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
    $this->db->delete('sys_object_to_service');
    $this->db->delete('sys_service');
    $this->db->delete('sys_object_to_node');
    $this->db->delete('sys_tree');
  }

  function testFetchById()
  {
    $toolkit =& Limb :: toolkit();

    $this->db->insert('sys_object', array('oid' => $id = 10,
                                          'class_id' => $class_id = 100));

    $this->db->insert('sys_class', array('id' => $class_id,
                                         'name' => $class_name = 'TestArticle'));

    $this->db->insert('sys_object_to_node', array('oid' => $id,
                                                  'node_id' => $node_id = 200));

    $this->db->insert('sys_tree', array('id' => $node_id,
                                        'parent_id' => $parent_node_id = 150,
                                        'identifier' => $identifier = 'test_identifier'));

    $this->db->insert('sys_object_to_service', array('id' => 50,
                                                     'oid' => $id,
                                                     'service_id' => $service_id = 5,
                                                     'title' => $title = 'test title'));

    $this->db->insert('sys_service', array('id' => $service_id,
                                           'name' => $service_name = 'ServiceName'));

    // junk records
    $this->db->insert('sys_object', array('oid' => $id2 = 11,
                                          'class_id' => $class_id2 = 101));

    $this->db->insert('sys_class', array('id' => $class_id2,
                                         'name' => $class_name2 = 'TestArticle2'));

    $this->db->insert('sys_object_to_node', array('oid' => $id2,
                                                  'node_id' => $node_id2 = 201));

    $this->db->insert('sys_tree', array('id' => $node_id2,
                                        'identifier' => $identifier2 = 'test_identifier2'));

    $this->db->insert('sys_object_to_service', array('id' => 51,
                                                     'oid' => $id2,
                                                     'service_id' => $service_id2 = 6,
                                                     'title' => $title2 = 'test title2'));

    $this->db->insert('sys_service', array('id' => $service_id2,
                                           'name' => $service_name2 = 'ArticleService'));

    $dao = new ServiceNodeDAO();

    $dataspace =& $dao->fetchById($id);

    $this->assertEqual($dataspace->get('class_name'), $class_name);
    $this->assertEqual($dataspace->get('_node_identifier'), $identifier);
    $this->assertEqual($dataspace->get('_node_id'), $node_id);
    $this->assertEqual($dataspace->get('_node_parent_id'), $parent_node_id);
    $this->assertEqual($dataspace->get('_service_name'), $service_name);
    $this->assertEqual($dataspace->get('_service_title'), $title);

    $dao->addCriteria(new SimpleConditionCriteria('tree.id = '. $node_id));
    $rs =& $dao->fetch();
    $this->assertEqual($rs->getTotalRowCount(), 1);

  }
}

?>