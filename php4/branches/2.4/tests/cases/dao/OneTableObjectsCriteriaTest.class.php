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
require_once(LIMB_DIR . '/core/dao/SiteObjectsDAO.class.php');
require_once(LIMB_DIR . '/core/dao/criteria/OneTableObjectsCriteria.class.php');
require_once(dirname(__FILE__) . '/SiteObjectsSQLBaseTest.class.php');
require_once(dirname(__FILE__) . '/DocumentTestDBTable.class.php');

class OneTableObjectsCriteriaTest extends SiteObjectsSQLBaseTest
{
  var $dao;

  function OneTableObjectsCriteriaTest()
  {
    parent :: SiteObjectsSQLBaseTest('one table objects criteria tests');
  }

  function setUp()
  {
    parent :: setUp();

    $this->dao = new SiteObjectsDAO();
    $this->dao->addCriteria(new OneTableObjectsCriteria('DocumentTest'));

    $this->_insertExtraTableRecords();
  }

  function _cleanUp()
  {
    parent :: _cleanUp();

    $this->db->delete('document');
  }

  function testCorrectLink()
  {
    $sql =& $this->dao->getSQL();
    $sql->addCondition('sso.id = 1');

    $rs =& new SimpleDbDataset($this->dao->fetch());
    $record = $rs->getRow();

    $this->assertEqual($record['identifier'], 'object_1');
    $this->assertEqual($record['title'], 'object_1_title');
    $this->assertEqual($record['content'], 'object_1_content');
    $this->assertEqual($record['annotation'], 'object_1_annotation');
  }

  function _insertExtraTableRecords()
  {
    $data = array();
    for($i = 1; $i <= 5; $i++)
    {
      $this->db->insert('document',
        array(
          'id' => $i+100,
          'object_id' => $i,
          'version' => 1,
          'identifier' => 'object_' . $i,
          'title' => 'object_' . $i . '_title',
          'content' => 'object_' . $i . '_content',
          'annotation' => 'object_' . $i . '_annotation',
        )
      );
    }
  }

}
?>
