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
require_once(LIMB_DIR . '/class/data_mappers/SiteObjectMapper.class.php');

class OneTableObjectsMapper extends SiteObjectMapper
{
  var $_db_table = null;

  function _getFinder()
  {
    include_once(LIMB_DIR . '/class/finders/FinderFactory.class.php');
    return FinderFactory :: create('OneTableObjectsRawFinder');
  }

  function getDbTable()
  {
    if(!$this->_db_table)
    {
      $db_table_name = $this->_defineDbTableName();

      $toolkit =& Limb :: toolkit();
      $this->_db_table =& $toolkit->createDBTable($db_table_name);
    }

    return $this->_db_table;
  }

  function _defineDbTableName(){die('abstract function! ' . __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);}

  //for mocking
  function _doParentInsert($site_object)
  {
    return parent :: insert($site_object);
  }

  //for mocking
  function _doParentUpdate($site_object)
  {
    parent :: update($site_object);
  }

  //for mocking
  function _doParentDelete($site_object)
  {
    parent :: delete($site_object);
  }

  function update($site_object)
  {
    $this->_doParentUpdate($site_object);

    $this->_updateLinkedTableRecord($site_object);
  }

  function insert($site_object)
  {
    $id = $this->_doParentInsert($site_object);

    $this->_insertLinkedTableRecord($site_object);

    return $id;
  }

  function delete($site_object)
  {
    $this->_doParentDelete($site_object);

    $this->_deleteLinkedTableRecord($site_object);
  }

  function _insertLinkedTableRecord($site_object)
  {
    $data = $site_object->export();
    $data['object_id'] = $site_object->getId();
    unset($data['id']);

    $table =& $this->getDbTable();
    $table->insert($data);
  }

  function _updateLinkedTableRecord($site_object)
  {
    $data = $site_object->export();
    unset($data['id']);

    $table =& $this->getDbTable();
    $table->update($data, array('object_id' => $site_object->getId()));
  }

  function _deleteLinkedTableRecord($site_object)
  {
    $db_table = $this->getDbTable();
    $db_table->delete(array('object_id' => $site_object->getId()));
  }
}

?>