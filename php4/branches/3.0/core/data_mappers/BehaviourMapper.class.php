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
require_once(LIMB_DIR . '/core/services/Service.class.php');

class ServiceMapper extends AbstractDataMapper
{
  function & findById($id)
  {
    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysService');

    if(!$record = $table->selectRecordById($id))
      return null;

    $service =& new Service($record->get('name'));
    $service->setId($id);

    return $service;
  }

  function insert(&$service)
  {
    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysService');

    $data['name'] = $service->getName();

    $rs =& $table->select(array('name' => $data['name']));
    $rs->rewind();

    if($rs->valid())
    {
      $record =& $rs->current();
      $id = $record->get('id');
    }
    else
      $id = $table->insert($data);

    $service->setId($id);

    return $id;
  }

  function update(&$service)
  {
    if(!$id = $service->getId())
      return throw(new LimbException('id is not set'));

    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysService');

    $data['name'] = $service->getName();

    return $table->updateById($id, $data);
  }

  function delete(&$service)
  {
    if(!$id = $service->getId())
      return throw(new LimbException('id is not set'));

    $toolkit =& Limb :: toolkit();
    $table =& $toolkit->createDBTable('SysService');

    return $table->deleteById($id);
  }

  function getIdsByNames($names)
  {
    $toolkit =& Limb :: toolkit();
    $db =& new SimpleDb($toolkit->getDbConnection());

    $rs =& $db->select('sys_service', array('id'), sqlIn('name', $names));

    $result = array();
    for($rs->rewind();$rs->valid();$rs->next())
    {
      $record = $rs->current();
      $result[] = $record->get('id');
    }

    return $result;
  }
}

?>
