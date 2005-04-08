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
class MapEntityPartToDataspaceCommand
{
  var $map;
  var $context_key;
  var $entity_part_name;

  function MapEntityPartToDataspaceCommand($map, $context_key, $entity_part_name)
  {
    $this->map = $map;
    $this->context_key = $context_key;
    $this->entity_part_name = $entity_part_name;
  }

  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    if(!$entity =& $context->getObject($this->context_key))
      return LIMB_STATUS_ERROR;

    if(!$part =& $entity->getPart($this->entity_part_name))
      return LIMB_STATUS_ERROR;

    foreach($this->map as $getter => $key)
      $dataspace->set($key, $part->get($getter));

    return LIMB_STATUS_OK;
  }
}


?>
