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

class ServiceMapper extends AbstractDataMapper
{
  function getIdentityKeyName()
  {
    return 'service_id';
  }

  function defineDataMap()
  {
    return array('service_id' => 'service_id',
                 'behaviour_id' => 'behaviour_id',
                 'title' => 'title',
                 'oid' => 'oid');
  }

  function load(&$record, &$service)
  {
    ComplexArray :: map($this->defineDataMap(), $record->export(), $raw_data);

    $service->merge($raw_data);

    $this->_doLoadBehaviour($record, $service);
  }

  function _doLoadBehaviour($record, &$service)
  {
    $mapper =& $this->_getBehaviourMapper();
    $behaviour =& $mapper->findById($record->get('behaviour_id'));

    $service->attachBehaviour($behaviour);
  }

  function &_getBehaviourMapper()
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createDataMapper('BehaviourMapper');
  }

  function insert(&$service)
  {
    $id = $this->_insertServiceRecord($service);

    $service->setServiceId($id);

    return $id;
  }

  function _insertServiceRecord(&$service)
  {
    if (!$service->getBehaviour())
      return throw(new LimbException('behaviour is not attached'));

    $toolkit =& Limb :: toolkit();

    $mapper =& $this->_getBehaviourMapper();
    $mapper->save($service->getBehaviour());

    ComplexArray :: map(array_flip($this->defineDataMap()), $service->export(), $raw_data);

    $bhvr =& $service->getBehaviour();
    $raw_data['behaviour_id'] = $bhvr->getId();

    $db_table = $toolkit->createDBTable('SysService');

    $id = $db_table->insert($raw_data);

    if(catch('Exception', $e))
      return throw($e);

    return $id;
  }

  function update(&$service)
  {
    if(!$service->getServiceId())
      return throw(new LimbException('service id not set'));

    if (!$service->getBehaviour())
      return throw(new LimbException('behaviour not attached'));

    $mapper =& $this->_getBehaviourMapper();
    $mapper->save($service->getBehaviour());

    ComplexArray :: map(array_flip($this->defineDataMap()), $service->export(), $raw_data);

    $bhvr =& $service->getBehaviour();
    $raw_data['behaviour_id'] = $bhvr->getId();

    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysService');
    $db_table->updateById($service->getServiceId(), $raw_data);
  }

  function getClassId($service)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysClass');

    $class_name = get_class($service);

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

    $insert_data['id'] = null;
    $insert_data['name'] = $class_name;

    return $db_table->insert($insert_data);
  }

  function delete(&$service)
  {
    if (!$this->canDelete($service))
      return;

    $this->_deleteServiceRecord($service);
  }

  function _deleteServiceRecord(&$service)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysService');
    $db_table->deleteById($service->getServiceId());
  }

  function canDelete(&$service)
  {
    if(!$service->getServiceId())
      return throw(new LimbException('service id not set'));

    return true;
  }

}

?>
