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
require_once(LIMB_DIR . '/core/DAO/criteria/OneTableObjectsCriteria.class.php');
require_once(LIMB_DIR . '/core/DAO/SQLBasedDAO.class.php');
require_once(dirname(__FILE__) . '/../orm/data_mappers/OneTableObjectMapperTestDbTable.class.php');

Mock :: generatePartial('SQLBasedDAO',
                        'SQLBasedDAOOTOCTestVersion',
                        array('_initSQL'));

class OneTableObjectsCriteriaTest extends LimbTestCase
{
  var $dao;
  var $sql;
  var $db;
  var $conn;

  function OneTableObjectsCriteriaTest()
  {
    parent :: LimbTestCase('one table objects criteria tests');
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $this->conn =& $toolkit->getDbConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->_cleanUp();

    $this->dao = new SQLBasedDAOOTOCTestVersion($this);

    $this->sql = new ComplexSelectSQL('SELECT sys_object.oid as oid %fields% FROM sys_object %tables% %where%');
    $this->dao->setReturnReference('_initSQL', $this->sql);

    $this->dao->SQLBasedDAO();

    $this->dao->addCriteria(new OneTableObjectsCriteria('OneTableObjectMapperTest'));

    $this->_insertObjectRecords();
    $this->_insertLinkedTableRecords();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('test_one_table_object');
    $this->db->delete('sys_object');
  }

  function testCorrectLink()
  {
    $this->sql->addCondition('sys_object.oid = 1');

    $rs =& new SimpleDbDataset($this->dao->fetch());
    $record = $rs->getRow();

    $this->assertEqual($record['content'], 'object_1_content');
    $this->assertEqual($record['annotation'], 'object_1_annotation');
  }

  function _insertObjectRecords()
  {
    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysObject');

    // Insert real records
    for($i = 1; $i <= 5; $i++)
    {
      $values['oid'] = $i;
      $values['class_id'] = 150;
      $table->insert($values);
    }
  }

  function _insertLinkedTableRecords()
  {
    $data = array();
    for($i = 1; $i <= 5; $i++)
    {
      $this->db->insert('test_one_table_object',
        array(
          'id' => $i+100,
          'oid' => $i,
          'content' => 'object_' . $i . '_content',
          'annotation' => 'object_' . $i . '_annotation',
        )
      );
    }
  }

}
?>
