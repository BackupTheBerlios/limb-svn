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

class OneTableObjectMapper extends AbstractDataMapper
{
  var $_db_table = null;

  function OneTableObjectMapper($db_name)
  {
    $this->_db_name = $db_name;
  }

  function &getDbTable()
  {
    if(!$this->_db_table)
    {
      $toolkit =& Limb :: toolkit();
      $this->_db_table =& $toolkit->createDBTable($this->_db_name);
    }

    return $this->_db_table;
  }

  function load(&$record, &$domain_object)
  {
    $raw_data = $record->export();

    $raw_data = $this->_filterInputData($raw_data);

    $domain_object->merge($raw_data);
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

  function update(&$domain_object)
  {
    $data = $domain_object->export();

    $table =& $this->getDbTable();
    $table->update($data, array('id' => $domain_object->getId()));
  }

  function insert(&$domain_object)
  {
    $raw_data = $this->_filterInputData($domain_object->export());
    $toolkit =& Limb :: toolkit();

    $id = $toolkit->nextUID();
    $raw_data['id'] = $id;

    $domain_object->setId($id);

    $table =& $this->getDbTable();
    return $table->insert($raw_data);
  }

  function delete(&$domain_object)
  {
    $db_table =& $this->getDbTable();
    $db_table->delete(array('id' => $domain_object->getId()));
  }
}

?>