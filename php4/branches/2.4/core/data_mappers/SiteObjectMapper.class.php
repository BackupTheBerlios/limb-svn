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

class SiteObjectMapper extends AbstractDataMapper
{
  function defineDataMap()
  {
    return array('id' => 'id',
                 'behaviour_id' => 'behaviour_id',
                 'locale_id' => 'locale_id',
                 'class_id' => 'class_id',
                 'creator_id' => 'creator_id',
                 'title' => 'title',
                 'modified_date' => 'modified_date',
                 'created_date' => 'created_date',
                 'current_version' => 'version');
  }

  function load(&$record, &$site_object)
  {
    ComplexArray :: map($this->defineDataMap(), $record->export(), $raw_data);

    $site_object->import($raw_data);

    $this->_doLoadBehaviour($record, $site_object);
  }

  function _doLoadBehaviour($record, &$site_object)
  {
    $mapper =& $this->_getBehaviourMapper();
    $behaviour =& $mapper->findById($record->get('behaviour_id'));

    $site_object->attachBehaviour($behaviour);
  }

  function &_getBehaviourMapper()
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createDataMapper('SiteObjectBehaviourMapper');
  }

  function insert(&$site_object)
  {
    $id = $this->_insertSiteObjectRecord($site_object);

    $site_object->setId($id);

    return $id;
  }

  function _insertSiteObjectRecord(&$site_object)
  {
    if (!$site_object->getBehaviour())
      return throw(new LimbException('behaviour is not attached'));

    if (!$this->getClassId($site_object))
      return throw(new LimbException('class id is empty'));

    if(!$site_object->getCreatedDate())
      $site_object->setCreatedDate(time());

    if(!$site_object->getModifiedDate())
      $site_object->setModifiedDate(time());

    if (!$site_object->getLocaleId())
      $site_object->setLocaleId($this->getParentLocaleId($site_object->getParentNodeId()));

    $site_object->setVersion(1);

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();

    $mapper =& $this->_getBehaviourMapper();
    $mapper->save($site_object->getBehaviour());

    $site_object->setCreatorId($user->getId());

    ComplexArray :: map(array_flip($this->defineDataMap()), $site_object->export(), $raw_data);

    $raw_data['class_id'] = $this->getClassId($site_object);

    $bhvr =& $site_object->getBehaviour();
    $raw_data['behaviour_id'] = $bhvr->getId();

    $sys_site_object_db_table = $toolkit->createDBTable('SysSiteObject');

    $id = $sys_site_object_db_table->insert($raw_data);

    if(catch('Exception', $e))
      return throw($e);

    return $id;
  }

  function update(&$site_object)
  {
    if(!$site_object->getId())
      return throw(new LimbException('object id not set'));

    if (!$site_object->getBehaviour())
      return throw(new LimbException('behaviour id not attached'));

    $mapper =& $this->_getBehaviourMapper();
    $mapper->save($site_object->getBehaviour());

    ComplexArray :: map(array_flip($this->defineDataMap()), $site_object->export(), $raw_data);
    unset($raw_data['created_date']);
    unset($raw_data['creator_id']);
    unset($raw_data['class_id']);

    $raw_data['modified_date'] = time();

    $site_object->setModifiedDate($raw_data['modified_date']);

    $bhvr =& $site_object->getBehaviour();
    $raw_data['behaviour_id'] = $bhvr->getId();

    $toolkit =& Limb :: toolkit();
    $sys_site_object_db_table =& $toolkit->createDBTable('SysSiteObject');
    return $sys_site_object_db_table->updateById($site_object->getId(), $raw_data);
  }

  function getClassId($site_object)
  {
    $toolkit =& Limb :: toolkit();
    $db_table =& $toolkit->createDBTable('SysClass');

    $class_name = get_class($site_object);

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

  function delete(&$site_object)
  {
    if (!$this->canDelete($site_object))
      return;

    $this->_deleteSiteObjectRecord($site_object);
  }

  function _deleteSiteObjectRecord(&$site_object)
  {
    $toolkit =& Limb :: toolkit();
    $sys_site_object_db_table =& $toolkit->createDBTable('SysSiteObject');
    $sys_site_object_db_table->deleteById($site_object->getId());
  }

  function canDelete(&$site_object)
  {
    if(!$site_object->getId())
      return throw(new LimbException('object id not set'));

    return true;
  }

}

?>
