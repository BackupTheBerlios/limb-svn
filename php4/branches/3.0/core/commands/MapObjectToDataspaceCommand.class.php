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
class MapObjectToDataspaceCommand
{
  var $map;
  var $object;

  function MapObjectToDataspaceCommand($map, &$object)
  {
    $this->map = $map;
    $this->object =& $object;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    foreach($this->map as $getter => $key)
      $dataspace->set($key, $this->object->get($getter));

    return LIMB_STATUS_OK;
  }
}


?>
