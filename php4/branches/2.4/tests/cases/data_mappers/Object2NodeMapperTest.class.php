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
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/core/data_mappers/Object2NodeMapper.class.php');
require_once(LIMB_DIR . '/core/site_objects/SiteObject.class.php');

class Object2NodeMapperTest extends LimbTestCase
{
  var $db;

  function Object2NodeMapperTest()
  {
    parent :: LimbTestCase('object 2 node mapper test');
  }

  function setUp()
  {
    $this->db =& new SimpleDb(LimbDbPool :: getConnection());

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_domain_object_to_node');
  }

  function testFailedInsertNoId()
  {
    $site_object = new SiteObject();

    $mapper = new Object2NodeMapper();

    $mapper->insert($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'uid is not set');
  }

  function testFailedInsertNoSiteObjectId()
  {
    $site_object = new SiteObject();
    $site_object->setId(10);

    $mapper = new Object2NodeMapper();

    $mapper->insert($site_object);
    $this->assertTrue(catch('Exception', $e));
    $this->assertEqual($e->getMessage(), 'site object id is not set');
  }

  function testInsertOk()
  {
    $mapper = new Object2NodeMapper();
    $site_object = new SiteObject();

    $site_object->setId($uid = 100);
    $site_object->setSiteObjectId($site_object_id = 500);

    $mapper->insert($site_object);

    $rs =& $this->db->select('sys_domain_object_to_node');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($arr[0]['uid'], $uid);
    $this->assertEqual($arr[0]['site_object_id'], $site_object_id);
  }

  function testUpdate()
  {
    $mapper = new Object2NodeMapper();
    $site_object = new SiteObject();

    $site_object->setId($uid = 100);
    $site_object->setSiteObjectId($site_object_id = 500);

    $this->db->insert('sys_domain_object_to_node',
                      array('uid' => 99,
                            'site_object_id' => $site_object_id));

    $mapper->update($site_object);

    $rs =& $this->db->select('sys_domain_object_to_node');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($arr[0]['uid'], $uid);
    $this->assertEqual($arr[0]['site_object_id'], $site_object_id);
  }

  function testDelete()
  {
    $mapper = new Object2NodeMapper();
    $site_object = new SiteObject();

    $site_object->setId($uid = 100);

    $this->db->insert('sys_domain_object_to_node',
                      array('uid' => $uid,
                            'site_object_id' => $site_object_id = 500));

    $this->db->insert('sys_domain_object_to_node',
                      array('uid' => 102,
                            'site_object_id' => 502));

    $mapper->delete($site_object);

    $rs =& $this->db->select('sys_domain_object_to_node');
    $arr = $rs->getArray();
    $this->assertEqual(sizeof($arr), 1);

    $this->assertEqual($arr[0]['uid'], 102);
    $this->assertEqual($arr[0]['site_object_id'], 502);
  }
}

?>