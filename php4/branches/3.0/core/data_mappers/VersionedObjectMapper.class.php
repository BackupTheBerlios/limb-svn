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

    $version_session_id = $this->_getVersionSessionId($object);

    $this->_insertCurrentVersionRecord($object, $version_session_id);

    $this->_insertVersionRecord($object, $version_session_id);
  }

  function _getVersionSessionId(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysCurrentVersion');

    $condition['revision_object_id'] = $object->get($this->delegated_mapper->getIdentityKeyName());
    $rs =& $db_table->select($condition);

    $rs->rewind();
    if($rs->valid())
    {
      $current =& $rs->current();
      return $current->get('version_session_id');
    }
    else
      return $toolkit->nextUID();
  }

  function _getLastVersion($version_session_id)
  {
    $toolkit =& Limb :: toolkit();
    $version_db_table =& $toolkit->createDBTable('SysVersionHistory');

    $db_table_name = $version_db_table->getTableName();

    //refactor LimbDBTable?
    $conn =& $toolkit->getDbConnection();
    $stmt = $conn->newStatement("SELECT MAX(version) as version FROM $db_table_name WHERE version_session_id=:id:");

    $stmt->setInteger('id', $version_session_id);
    if(!$value = $stmt->getOneValue())
      return 0;
    else
      return $value;
  }

  function _insertVersionRecord(&$object, $version_session_id, $version = 1)
  {
    $toolkit =& Limb :: toolkit();
    $version_db_table =& $toolkit->createDBTable('SysVersionHistory');

    $user =& $toolkit->getUser();

    $data['revision_object_id'] = $object->get($this->delegated_mapper->getIdentityKeyName());
    $data['version_session_id'] = $version_session_id;
    $data['version'] = $version;
    $data['created_date'] = time();
    $data['creator_id'] = $user->getId();

    $version_db_table->insert($data);
  }

  function _insertCurrentVersionRecord(&$object, $version_session_id)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysCurrentVersion');

    $row['revision_object_id'] = $object->get($this->delegated_mapper->getIdentityKeyName());
    $row['version_session_id'] = $version_session_id;
    $db_table->insert($row);
  }

  function _updateCurrentVersionRecord(&$object, $version_session_id)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysCurrentVersion');

    $db_table->update(array('revision_object_id' => $object->get($this->delegated_mapper->getIdentityKeyName())),
                      array('version_session_id' => $version_session_id));
  }

  function update(&$object)
  {
    $version_session_id = $this->_getVersionSessionId($object);

    $this->delegated_mapper->insert($object);

    $version = $this->_getLastVersion($version_session_id) + 1;

    $this->_insertVersionRecord($object, $version_session_id, $version);

    $this->_updateCurrentVersionRecord(&$object, $version_session_id);
  }

  function delete(&$object)
  {
    $version_session_id = $this->_getVersionSessionId($object);

    $this->delegated_mapper->delete($object);

    $this->_deleteVersionRecords($object, $version_session_id);

    $this->_deleteCurrentVersionRecord($object, $version_session_id);
  }

  function _deleteVersionRecords(&$object, $version_session_id)
  {
    $toolkit =& Limb :: toolkit();
    $version_db_table =& $toolkit->createDBTable('SysVersionHistory');
    $version_db_table->delete(array('version_session_id' => $version_session_id));
  }

  function _deleteCurrentVersionRecord(&$object, $version_session_id)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysCurrentVersion');
    $db_table->delete(array('version_session_id' => $version_session_id));
  }
}

?>