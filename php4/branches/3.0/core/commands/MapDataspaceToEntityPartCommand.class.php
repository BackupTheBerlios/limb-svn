<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: EditSimpleObjectCommand.class.php 1186 2005-03-23 09:47:34Z seregalimb $
*
***********************************************************************************/

class MapDataspaceToEntityPartCommand
{
  var $map;
  var $context_key;
  var $entity_part_name;

  function MapDataspaceToEntityPartCommand($map, $context_key, $entity_part_name)
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

    foreach($this->map as $key => $setter)
    {
      if ((($value = $dataspace->get($key)) !== false) && (($value = $dataspace->get($key)) !== null))
        $part->set($setter, $value);
    }

    return LIMB_STATUS_OK;
  }
}

?>
