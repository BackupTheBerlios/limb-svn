<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ServiceLocationMapper.class.php 1343 2005-06-01 08:16:13Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/orm/data_mappers/AbstractDataMapper.class.php');

class ServiceLocationMapper extends AbstractDataMapper
{
  function load(&$record, &$object)
  {
    $object->set('id', $record->get('_service_id'));
    $object->set('title', $record->get('_service_title'));
    $object->set('name', $record->get('_service_name'));
  }

  function insert(&$object)
  {
    if (!$object->get('oid'))
      return throw_error(new LimbException('oid is not set'));

    $toolkit =& Limb :: toolkit();
    $service_db_table =& $toolkit->createDBTable('SysService');

    $service_id = $this->_getServiceId($object);

    $db_table =& $toolkit->createDBTable('SysObject2Service');

    $row = array('oid' => $object->get('oid'),
                 'service_id' => $service_id,
                 'title' => $object->get('title'),
                 );

    $db_table->insert($row);
  }

  function update(&$object)
  {
    $toolkit =& Limb :: toolkit();

    $service_id = $this->_getServiceId($object);

    $db_table =& $toolkit->createDBTable('SysObject2Service');

    $condition['oid'] = $object->get('oid');
    $rs = $db_table->select($condition);
    $rs->rewind();
    if($rs->valid())
    {
      $row['service_id'] = $service_id;
      $row['title'] = $object->get('title');
      $db_table->update($row, $condition);
    }
    else
    {
      $row['oid'] = $object->get('oid');
      $row['service_id'] = $service_id;
      $row['title'] = $object->get('title');
      $db_table->insert($row);
    }
  }

  function delete(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysObject2Service');
    $db_table->delete(array('oid' => $object->get('oid')));
  }

  function _getServiceId(&$object)
  {
    $toolkit =& Limb :: toolkit();
    $service_db_table =& $toolkit->createDBTable('SysService');

    $rs =& $service_db_table->select(array('name' => $object->get('name')));

    $rs->rewind();
    if(!$rs->valid())
    {
      $row = array('name' => $object->get('name'));
      $service_id = $service_db_table->insert($row);
    }
    else
    {
      $record = $rs->current();
      $service_id = $record->get('id');
    }

    return $service_id;
  }
}

?>