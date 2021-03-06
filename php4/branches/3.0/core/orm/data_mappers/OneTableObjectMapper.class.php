<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: OneTableObjectMapper.class.php 1353 2005-06-08 14:52:48Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/orm/data_mappers/AbstractDataMapper.class.php');

define('EMPTY_RECORD_PREFIX', '');

class OneTableObjectMapper extends AbstractDataMapper
{
  var $_db_table = null;
  var $_record_prefix = null;

  function OneTableObjectMapper($db_name, $record_prefix = '_content_')
  {
    $this->_db_name = $db_name;
    $this->_record_prefix = $record_prefix;
  }

  function & getDbTable()
  {
    if(!$this->_db_table)
    {
      $toolkit =& Limb :: toolkit();
      $this->_db_table =& $toolkit->createDBTable($this->_db_name);
    }

    return $this->_db_table;
  }

  function load(&$record, &$object, $override_prefix = null)
  {
    $table =& $this->getDbTable();

    $prefix = $override_prefix ? $override_prefix : $this->_record_prefix;
    foreach($table->getColumns() as $key => $props)
    {
      if($value = $record->get($prefix . $key))
       $object->set($key, $value);
    }
  }

  function _filterInputData($row)
  {
    $table =& $this->getDbTable();

    $filtered = array();
    foreach($row as $key => $value)
    {
      if($table->hasColumn($key))
        $filtered[$key] = $value;
    }
    return $filtered;
  }

  function update(&$object)
  {
    $data = $object->export();

    $table =& $this->getDbTable();
    $table->update($data, array($table->getPrimaryKeyName() => $object->get($table->getPrimaryKeyName())));
  }

  function insert(&$object)
  {
    $raw_data = $this->_filterInputData($object->export());
    $toolkit =& Limb :: toolkit();
    $table =& $this->getDbTable();

    return $table->insert($raw_data);
  }

  function delete(&$object)
  {
    $table =& $this->getDbTable();
    $table->delete(array($table->getPrimaryKeyName() => $object->get($table->getPrimaryKeyName())));
  }
}

?>