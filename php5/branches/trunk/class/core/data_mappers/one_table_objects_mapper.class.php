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
require_once(LIMB_DIR . '/class/core/data_mappers/site_object_mapper.class.php');

abstract class one_table_objects_mapper extends site_object_mapper
{
  protected  $_db_table = null;

  protected function _get_finder()
  {
    include_once(LIMB_DIR . '/class/core/finders/finder_factory.class.php');
    return finder_factory :: create('one_table_objects_raw_finder');
  }

  public function get_db_table()
  {
    if(!$this->_db_table)
    {
      $db_table_name = $this->_define_db_table_name();

      $this->_db_table = Limb :: toolkit()->createDBTable($db_table_name);
    }

    return $this->_db_table;
  }

  abstract protected function _define_db_table_name();

  //for mocking
  protected function _do_parent_insert($site_object)
  {
    return parent :: insert($site_object);
  }

  //for mocking
  protected function _do_parent_update($site_object)
  {
    parent :: update($site_object);
  }

  //for mocking
  protected function _do_parent_delete($site_object)
  {
    parent :: delete($site_object);
  }

  public function update($site_object)
  {
    $this->_do_parent_update($site_object);

    $this->_update_linked_table_record($site_object);
  }

  public function insert($site_object)
  {
    $id = $this->_do_parent_insert($site_object);

    $this->_insert_linked_table_record($site_object);

    return $id;
  }

  public function delete($site_object)
  {
    $this->_do_parent_delete($site_object);

    $this->_delete_linked_table_record($site_object);
  }

  protected function _insert_linked_table_record($site_object)
  {
    $data = $site_object->export();
    $data['object_id'] = $site_object->get_id();
    unset($data['id']);

    $this->get_db_table()->insert($data);
  }

  protected function _update_linked_table_record($site_object)
  {
    $data = $site_object->export();
    unset($data['id']);

    $this->get_db_table()->update($data, array('object_id' => $site_object->get_id()));
  }

  protected function _delete_linked_table_record($site_object)
  {
    $db_table = $this->get_db_table();
    $db_table->delete(array('object_id' => $site_object->get_id()));
  }
}

?>