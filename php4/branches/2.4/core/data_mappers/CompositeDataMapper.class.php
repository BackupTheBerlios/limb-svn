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

class CompositeDataMapper
{
  var $mappers;

  function registerMapper(&$mapper)
  {
    $this->mappers[] =& $mapper;
  }

  function load(&$record, &$domain_object)
  {
    foreach(array_keys($this->mappers) as $key)
      $this->mappers[$key]->load($record, $domain_object);
  }

  function update(&$domain_object)
  {
    foreach(array_keys($this->mappers) as $key)
      $this->mappers[$key]->update($domain_object);
  }

  function insert(&$domain_object)
  {
    foreach(array_keys($this->mappers) as $key)
      $this->mappers[$key]->insert($domain_object);
  }

  function delete(&$domain_object)
  {
    foreach(array_keys($this->mappers) as $key)
      $this->mappers[$key]->delete($domain_object);
  }

}

?>
