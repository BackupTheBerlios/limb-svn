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
require_once(LIMB_DIR . '/core/dao/SiteObjectsRawSQL.class.php');
require_once(LIMB_DIR . '/core/db_tables/LimbDbTableFactory.class.php');
require_once(LIMB_DIR . '/core/tree/MaterializedPathTree.class.php');

SimpleTestOptions :: ignore('SiteObjectsSQLBaseTest');

class SiteObjectsSQLBaseTest extends LimbTestCase
{
  var $behaviour_id;
  var $class_id;
  var $root_node_id;
  var $object2node = array();
  var $db;
  var $conn;
  var $sql;

  function setUp()
  {
    $this->conn =& LimbDbPool :: getConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->_cleanUp();

    $this->_insertSysClassRecord();
    $this->_insertSysBehaviourRecord();

    $this->_insertSysSiteObjectRecords();
    $this->_insertFakeSysSiteObjectRecords();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_site_object');
    $this->db->delete('sys_site_object_tree');
    $this->db->delete('sys_class');
    $this->db->delete('sys_behaviour');
  }

  function _insertSysClassRecord()
  {
    $db_table = LimbDbTableFactory :: create('SysClass');
    $this->class_id = $db_table->insert(array('name' => 'site_object'));
  }

  function _insertSysBehaviourRecord()
  {
    $db_table = LimbDbTableFactory :: create('SysBehaviour');
    $this->behaviour_id = $db_table->insert(array('name' => 'site_object_behaviour'));
  }

  function _insertSysSiteObjectRecords()
  {
    $tree = new MaterializedPathTree();

    $values['identifier'] = 'root';
    $this->root_node_id = $tree->createRootNode($values, false, true);

    $data = array();
    for($i = 1; $i <= 5; $i++)
    {
      $values['identifier'] = 'object_' . $i;
      $values['object_id'] = $i;
      $this->object2node[$i] = $tree->createSubNode($this->root_node_id, $values);

      $this->db->insert('sys_site_object',
        array(
          'id' => $i,
          'class_id' => $this->class_id,
          'behaviour_id' => $this->behaviour_id,
          'title' => 'object_' . $i . '_title',
          'locale_id' => 'en',
          'node_id' => $this->object2node[$i]
        )
      );
    }
  }

  function _insertFakeSysSiteObjectRecords()
  {
    $class_db_table = LimbDbTableFactory :: create('SysClass');
    $class_db_table->insert(array('id' => 1001, 'class_name' => 'fake_class'));

    $tree = new MaterializedPathTree();

    $db_table =& LimbDbTableFactory :: create('SysSiteObject');

    $data = array();
    for($i = 6; $i <= 10 ; $i++)
    {
      $values['identifier'] = 'object_' . $i;
      $values['object_id'] = $i;
      $node_id = $tree->createSubNode($this->root_node_id, $values);

      $this->db->insert('sys_site_object',
        array(
          'id' => $i,
          'class_id' => 1001,
          'behaviour_id' => $this->behaviour_id,
          'title' => 'object_' . $i . '_title',
          'locale_id' => 'en',
          'node_id' => $node_id
        )
      );
    }
  }

}
?>
