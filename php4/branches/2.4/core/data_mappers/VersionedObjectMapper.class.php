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
  }

  function insert(&$object)
  {
    $this->delegated_mapper->insert($object);

    $version_uid = $this->_getVersionUID($object);

    $this->_insertCurrentVersionRecord($object, $version_uid);

    $this->_insertVersionRecord($object, $version_uid);
  }

  function _getVersionUID(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysCurrentVersion');

    $condition['uid'] = $object->getId();
    $rs =& $db_table->select($condition);

    $rs->rewind();
    if($rs->valid())
    {
      $current =& $rs->current();
      return $current->get('version_uid');
    }
    else
      return $toolkit->nextUID();
  }

  function _getLastVersion($version_uid)
  {
    $toolkit =& Limb :: toolkit();
    $version_db_table =& $toolkit->createDBTable('SysVersionHistory');

    $db_table_name = $version_db_table->getTableName();

    //refactor LimbDBTable?
    $conn =& $toolkit->getDbConnection();
    $stmt = $conn->newStatement("SELECT MAX(version) as version FROM $db_table_name WHERE version_uid=:id:");

    $stmt->setInteger('id', $version_uid);
    if(!$value = $stmt->getOneValue())
      return 0;
    else
      return $value;
  }

  function _insertVersionRecord(&$object, $version_uid, $version = 1)
  {
    $toolkit =& Limb :: toolkit();
    $version_db_table =& $toolkit->createDBTable('SysVersionHistory');

    $user =& $toolkit->getUser();

    $data['uid'] = $object->getId();
    $data['version_uid'] = $version_uid;
    $data['version'] = $version;
    $data['created_date'] = time();
    $data['creator_id'] = $user->getId();

    $version_db_table->insert($data);
  }

  function _insertCurrentVersionRecord(&$object, $version_uid)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysCurrentVersion');

    $row['uid'] = $object->getId();
    $row['version_uid'] = $version_uid;
    $db_table->insert($row);
  }

  function _updateCurrentVersionRecord(&$object, $version_uid)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysCurrentVersion');

    $db_table->update(array('uid' => $object->getId()), array('version_uid' => $version_uid));
  }

  function update(&$object)
  {
    $version_uid = $this->_getVersionUID($object);

    $this->delegated_mapper->insert($object);

    $version = $this->_getLastVersion($version_uid) + 1;

    $this->_insertVersionRecord($object, $version_uid, $version);

    $this->_updateCurrentVersionRecord(&$object, $version_uid);
  }

  function delete(&$object)
  {
    $version_uid = $this->_getVersionUID($object);

    $this->delegated_mapper->delete($object);

    $this->_deleteVersionRecords($object, $version_uid);

    $this->_deleteCurrentVersionRecord($object, $version_uid);
  }

  function _deleteVersionRecords(&$object, $version_uid)
  {
    $toolkit =& Limb :: toolkit();
    $version_db_table =& $toolkit->createDBTable('SysVersionHistory');
    $version_db_table->delete(array('version_uid' => $version_uid));
  }

  function _deleteCurrentVersionRecord(&$object, $version_uid)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysCurrentVersion');
    $db_table->delete(array('version_uid' => $version_uid));
  }
}

?>