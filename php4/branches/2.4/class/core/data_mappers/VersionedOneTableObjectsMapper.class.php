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
require_once(LIMB_DIR . '/class/core/data_mappers/OneTableObjectsMapper.class.php');

abstract class VersionedOneTableObjectsMapper extends OneTableObjectsMapper
{
  function findByVersion($id, $version)
  {
    $raw_data = $this->_getFinder()->findByVersion($id, $version);

    if(!$raw_data)
      return null;

    $domain_object = $this->_createDomainObject();

    $this->_doLoad($raw_data, $domain_object);

    return $domain_object;
  }

  function trimVersions($object_id, $version)
  {
    $this->getDbTable()->delete('object_id = ' . $object_id .
                                  ' AND version <> ' . $version);

    $version_db_table = Limb :: toolkit()->createDBTable('SysObjectVersion');
    $version_db_table->delete('object_id = ' . $object_id .
                              ' AND version <> ' . $version);
  }

  function insert($site_object)
  {
    $id = $this->_doParentInsert($site_object);

    $this->_insertVersionRecord($site_object);

    return $id;
  }

  //for mocking
  function _doParentInsert($site_object)
  {
    return parent :: insert($site_object);
  }

  function _insertVersionRecord($site_object)
  {
    $version_db_table = Limb :: toolkit()->createDBTable('SysObjectVersion');

    $site_object->setCreatedDate(time());
    $site_object->setModifiedDate(time());

    $user = Limb :: toolkit()->getUser();
    $site_object->setCreatorId($user->getId());

    $data['object_id'] = $site_object->getId();
    $data['version'] = $site_object->getVersion();
    $data['created_date'] = $site_object->getCreatedDate();
    $data['modified_date'] = $site_object->getModifiedDate();

    $data['creator_id'] = $site_object->getCreatorId();

    $version_db_table->insert($data);
  }

  function _updateVersionRecord($site_object)
  {
    $version_db_table = Limb :: toolkit()->createDBTable('SysObjectVersion');

    $site_object->setModifiedDate(time());

    $data['modified_date'] = $site_object->getModifiedDate();

    $version_db_table->update($data, array('object_id' => $site_object->getId(),
                                           'version' => $site_object->getVersion()));
  }

  function update($site_object)
  {
    if ($site_object->isNewVersion())
    {
      $site_object->setVersion($site_object->getVersion() + 1);

      $this->_insertVersionRecord($site_object);

      $this->_insertLinkedTableRecord($site_object);
    }
    else
    {
      $this->_updateVersionRecord($site_object);
    }

    $this->_doParentUpdate($site_object);
  }

  //for mocking
  function _doParentUpdate($site_object)
  {
    parent :: update($site_object);
  }

  function delete($site_object)
  {
    $this->_doParentDelete($site_object);

    $this->_deleteVersionRecords($site_object);
  }

  //for mocking
  function _doParentDelete($site_object)
  {
    parent :: delete($site_object);
  }

  function _deleteVersionRecords($site_object)
  {
    $version_db_table = Limb :: toolkit()->createDBTable('SysObjectVersion');
    $version_db_table->delete(array('object_id' => $site_object->getId()));
  }
}

?>