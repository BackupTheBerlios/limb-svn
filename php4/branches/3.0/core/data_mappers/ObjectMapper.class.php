<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ServiceMapper.class.php 1085 2005-02-02 16:04:20Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/data_mappers/AbstractDataMapper.class.php');

class ObjectMapper extends AbstractDataMapper
{
  function getIdentityKeyName()
  {
    return 'oid';
  }

  function load(&$record, &$object)
  {
    $object->__class_name = $record->get('class_name');
    $object->set('oid', $record->get('oid'));
    $object->set('class_id', $record->get('class_id'));
  }

  function insert(&$object)
  {
    if (!$class_id = $this->getClassId($object))
      return throw(new LimbException('class id is empty'));

    $toolkit =& Limb :: toolkit();
    $db_table = $toolkit->createDBTable('SysObject');
    $id = $toolkit->nextUID();

    $raw_data['class_id'] = $class_id;
    $raw_data['oid'] = $id;
    $db_table->insert($raw_data);

    if(catch('Exception', $e))
      return throw($e);

    $object->set('class_id', $class_id);
    $object->set('oid', $id);
  }

  function getClassId($object)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysClass');

    $class_name = $object->__class_name;

    $rs =& $db_table->select(array('name' => $class_name));

    $count = $rs->getTotalRowCount();

    if ($count == 1)
    {
      $rs->rewind();
      $record = $rs->current();
      return $record->get('id');
    }
    elseif($count > 1)
    {
      return throw(new LimbException('there are more than 1 type found',
        array('name' => $class_name)));
    }

    $insert_data['name'] = $class_name;

    return $db_table->insert($insert_data);
  }

  function delete(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysObject');
    $db_table->deleteById($object->get('oid'));
  }
}

?>