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
require_once(LIMB_DIR . '/class/core/data_mappers/SiteObjectMapper.class.php');

abstract class OneTableObjectsMapper extends SiteObjectMapper
{
  protected  $_db_table = null;

  protected function _getFinder()
  {
    include_once(LIMB_DIR . '/class/core/finders/FinderFactory.class.php');
    return FinderFactory :: create('OneTableObjectsRawFinder');
  }

  public function getDbTable()
  {
    if(!$this->_db_table)
    {
      $db_table_name = $this->_defineDbTableName();

      $this->_db_table = Limb :: toolkit()->createDBTable($db_table_name);
    }

    return $this->_db_table;
  }

  abstract protected function _defineDbTableName();

  //for mocking
  protected function _doParentInsert($site_object)
  {
    return parent :: insert($site_object);
  }

  //for mocking
  protected function _doParentUpdate($site_object)
  {
    parent :: update($site_object);
  }

  //for mocking
  protected function _doParentDelete($site_object)
  {
    parent :: delete($site_object);
  }

  public function update($site_object)
  {
    $this->_doParentUpdate($site_object);

    $this->_updateLinkedTableRecord($site_object);
  }

  public function insert($site_object)
  {
    $id = $this->_doParentInsert($site_object);

    $this->_insertLinkedTableRecord($site_object);

    return $id;
  }

  public function delete($site_object)
  {
    $this->_doParentDelete($site_object);

    $this->_deleteLinkedTableRecord($site_object);
  }

  protected function _insertLinkedTableRecord($site_object)
  {
    $data = $site_object->export();
    $data['object_id'] = $site_object->getId();
    unset($data['id']);

    $this->getDbTable()->insert($data);
  }

  protected function _updateLinkedTableRecord($site_object)
  {
    $data = $site_object->export();
    unset($data['id']);

    $this->getDbTable()->update($data, array('object_id' => $site_object->getId()));
  }

  protected function _deleteLinkedTableRecord($site_object)
  {
    $db_table = $this->getDbTable();
    $db_table->delete(array('object_id' => $site_object->getId()));
  }
}

?>