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
require_once(LIMB_DIR . '/core/data_mappers/AbstractDataMapper.class.php');

class VersionedObjectMapper extends AbstractDataMapper
{
  var $delegated_mapper;

  function VersionedObjectMapper(&$delegated_mapper)
  {
    $this->delegated_mapper =& $delegated_mapper;
  }

  function load(&$record, &$object)
  {
    $this->delegated_mapper->load($record, $object);

    $object->setVersionUid($record->get('version_uid'));
    $object->setVersion($record->get('version'));
  }

  function insert(&$object)
  {
    $this->delegated_mapper->insert($object);

    $toolkit =& Limb :: toolkit();
    $object->setVersionUid($toolkit->nextUID());

    $this->_insertVersionRecord($object);

    $this->_updateCurrentVersionRecord(&$object);
  }

  function _insertVersionRecord(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $version_db_table =& $toolkit->createDBTable('SysVersionHistory');

    $user =& $toolkit->getUser();

    $data['uid'] = $object->getId();
    $data['version_uid'] = $object->getVersionUid();
    $data['version'] = $object->getVersion();
    $data['created_date'] = time();
    $data['creator_id'] = $user->getId();

    $version_db_table->insert($data);
  }

  function _updateCurrentVersionRecord(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $current_version_db_table =& $toolkit->createDBTable('SysCurrentVersion');

    $condition['version_uid'] = $object->getVersionUid();
    $rs = $current_version_db_table->select($condition);
    $rs->rewind();
    if($rs->valid())
    {
      $row['uid'] = $object->getId();
      $current_version_db_table->update($row, $condition);
    }
    else
    {
      $row['uid'] = $object->getId();
      $row['version_uid'] = $object->getVersionUid();
      $current_version_db_table->insert($row);
    }
  }

  function update(&$object)
  {
    if ($object->isNewVersion())
    {
      $object->setVersion($object->getVersion() + 1);

      $this->_insertVersionRecord($object);

      $this->delegated_mapper->insert($object);

      $this->_updateCurrentVersionRecord(&$object);
    }
    else
      $this->delegated_mapper->update($object);
  }

  function delete(&$object)
  {
    $this->delegated_mapper->delete($object);

    $this->_deleteVersionRecords($object);

    $this->_deleteCurrentVersionRecord($object);
  }

  function _deleteVersionRecords(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $version_db_table =& $toolkit->createDBTable('SysVersionHistory');
    $version_db_table->delete(array('version_uid' => $object->getVersionUId()));
  }

  function _deleteCurrentVersionRecord(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysCurrentVersion');
    $db_table->delete(array('version_uid' => $object->getVersionUId()));
  }
}

?>