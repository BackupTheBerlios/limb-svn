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
require_once(LIMB_DIR . '/class/core/data_mappers/one_table_objects_mapper.class.php');

abstract class versioned_one_table_objects_mapper extends one_table_objects_mapper
{
  public function find_by_version($id, $version)
  {
    $raw_data = $this->_get_finder()->find_by_version($id, $version);

    if(!$raw_data)
      return null;

    $domain_object = $this->_create_domain_object();

    $this->_do_load($raw_data, $domain_object);

    return $domain_object;
  }

  function trim_versions($object_id, $version)
  {
    $this->get_db_table()->delete('object_id = ' . $object_id .
                                  ' AND version <> ' . $version);

    $version_db_table = Limb :: toolkit()->createDBTable('sys_object_version');
    $version_db_table->delete('object_id = ' . $object_id .
                              ' AND version <> ' . $version);
  }

  public function insert($site_object)
  {
    $id = $this->_do_parent_insert($site_object);

    $this->_insert_version_record($site_object);

    return $id;
  }

  //for mocking
  protected function _do_parent_insert($site_object)
  {
    return parent :: insert($site_object);
  }

  protected function _insert_version_record($site_object)
  {
    $version_db_table = Limb :: toolkit()->createDBTable('sys_object_version');

    $site_object->set_created_date(time());
    $site_object->set_modified_date(time());

    $user = Limb :: toolkit()->getUser();
    $site_object->set_creator_id($user->get_id());

    $data['object_id'] = $site_object->get_id();
    $data['version'] = $site_object->get_version();
    $data['created_date'] = $site_object->get_created_date();
    $data['modified_date'] = $site_object->get_modified_date();

    $data['creator_id'] = $site_object->get_creator_id();

    $version_db_table->insert($data);
  }

  protected function _update_version_record($site_object)
  {
    $version_db_table = Limb :: toolkit()->createDBTable('sys_object_version');

    $site_object->set_modified_date(time());

    $data['modified_date'] = $site_object->get_modified_date();

    $version_db_table->update($data, array('object_id' => $site_object->get_id(),
                                           'version' => $site_object->get_version()));
  }

  public function update($site_object)
  {
    if ($site_object->is_new_version())
    {
      $site_object->set_version($site_object->get_version() + 1);

      $this->_insert_version_record($site_object);

      $this->_insert_linked_table_record($site_object);
    }
    else
    {
      $this->_update_version_record($site_object);
    }

    $this->_do_parent_update($site_object);
  }

  //for mocking
  protected function _do_parent_update($site_object)
  {
    parent :: update($site_object);
  }

  public function delete($site_object)
  {
    $this->_do_parent_delete($site_object);

    $this->_delete_version_records($site_object);
  }

  //for mocking
  protected function _do_parent_delete($site_object)
  {
    parent :: delete($site_object);
  }

  protected function _delete_version_records($site_object)
  {
    $version_db_table = Limb :: toolkit()->createDBTable('sys_object_version');
    $version_db_table->delete(array('object_id' => $site_object->get_id()));
  }
}

?>