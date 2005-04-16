<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: MapEntityPartToDataspaceCommand.class.php 1209 2005-04-08 14:29:41Z pachanga $
*
***********************************************************************************/
class MapContextObjectToDataspaceCommand
{
  var $map;
  var $field_name;

  function MapContextObjectToDataspaceCommand($field_name, $map)
  {
    $this->map = $map;
    $this->field_name = $field_name;
  }

  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    if(!$object =& $context->getObject($this->field_name))
      return LIMB_STATUS_ERROR;


    foreach($this->map as $getter => $key)
      $dataspace->set($key, $object->get($getter));

    return LIMB_STATUS_OK;
  }
}


?>
