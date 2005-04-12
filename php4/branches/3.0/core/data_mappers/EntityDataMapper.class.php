<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: TreeNodeDataMapper.class.php 1161 2005-03-14 16:55:07Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/data_mappers/AbstractDataMapper.class.php');

class EntityDataMapper extends AbstractDataMapper
{
  var $part_mappers = array();

  function registerPartMapper($name, &$mapper)
  {
    $this->part_mappers[$name] =& $mapper;
  }

  function load(&$record, &$entity)
  {
    $object_mapper =& $this->_createObjectMapper();
    $object_mapper->load($record, $entity);

    $toolkit =& Limb :: toolkit();
    $parts =& $entity->getParts();

    foreach(array_keys($parts) as $key)
    {
      $part =& $parts[$key];
      $mapper =& $this->_getPartMapperUseDefaultIfNone($key, $part);
      $mapper->load($record, $part);
      $part->set('oid', $record->get('oid'));
    }
  }

  function save(&$entity)
  {
    $object_mapper =& $this->_createObjectMapper();
    $object_mapper->save($entity);

    $toolkit =& Limb :: toolkit();

    $parts =& $entity->getParts();

    foreach(array_keys($parts) as $key)
    {
      $part =& $parts[$key];
      $mapper =& $this->_getPartMapperUseDefaultIfNone($key, $part);
      $part->set('oid', $entity->get('oid'));
      $mapper->save($part);
    }
  }

  function delete(&$entity)
  {
    $object_mapper =& $this->_createObjectMapper();
    $object_mapper->delete($entity);

    $parts =& $entity->getParts();

    foreach(array_keys($parts) as $key)
    {
      $part =& $parts[$key];
      $mapper =& $this->_getPartMapperUseDefaultIfNone($key, $part);
      $mapper->delete($part);
    }
  }

  function & _createObjectMapper()
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->createDataMapper('ObjectMapper');
  }

  function & _getPartMapperUseDefaultIfNone($name, &$part)
  {
    if(isset($this->part_mappers[$name]))
    {
      $mapper =& Handle :: resolve($this->part_mappers[$name]);
      $this->part_mappers[$name] =& $mapper;
      return $this->part_mappers[$name];
    }

    $toolkit =& Limb :: toolkit();
    return $toolkit->createDataMapper($part->__class_name . 'Mapper'); //PHP4 dirty workaround
  }
}

?>