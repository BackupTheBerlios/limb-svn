<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CompositeMapper.class.php 1161 2005-03-14 16:55:07Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/orm/data_mappers/AbstractDataMapper.class.php');

class CompositeMapper extends AbstractDataMapper
{
  var $mappers;

  function CompositeMapper()
  {
  }

  function registerMapper(&$mapper)
  {
    $this->mappers[] =& $mapper;
  }

  function load(&$record, &$domain_object)
  {
    foreach(array_keys($this->mappers) as $key)
    {
      $this->mappers[$key] =& Handle :: resolve($this->mappers[$key]);
      $this->mappers[$key]->load($record, $domain_object);
    }
  }

  function update(&$domain_object)
  {
    foreach(array_keys($this->mappers) as $key)
    {
      $this->mappers[$key] =& Handle :: resolve($this->mappers[$key]);
      $this->mappers[$key]->update($domain_object);
    }
  }

  function insert(&$domain_object)
  {
    foreach(array_keys($this->mappers) as $key)
    {
      $this->mappers[$key] =& Handle :: resolve($this->mappers[$key]);
      $this->mappers[$key]->insert($domain_object);
    }
  }

  function delete(&$domain_object)
  {
    foreach(array_keys($this->mappers) as $key)
    {
      $this->mappers[$key] =& Handle :: resolve($this->mappers[$key]);
      $this->mappers[$key]->delete($domain_object);
    }
  }

}

?>
