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
class MapObjectToDataspaceCommand
{
  var $map;
  var $object_handle;

  function MapObjectToDataspaceCommand($map)
  {
    $this->map = $map;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    if(!$object =& $toolkit->getProcessedObject())
      return LIMB_STATUS_ERROR;

    $dataspace =& $toolkit->getDataspace();

    foreach($this->map as $getter => $key)
      $dataspace->set($key, $object->get($getter));

    return LIMB_STATUS_OK;
  }
}


?>
