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

class AbstractDataMapper
{
  function load(&$record, &$domain_object){}

  function save(&$domain_object)
  {
    if($domain_object->getId())
      $this->update($domain_object);
    else
      $this->insert($domain_object);
  }

  function insert(&$domain_object){}

  function update(&$domain_object){}

  function delete(&$domain_object){}
}

?>
