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

class MapDataspaceToObjectCommand
{
  var $map;

  function MapDataspaceToObjectCommand($map)
  {
    $this->map = $map;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();

    if(!$object =& $toolkit->getProcessedObject())
      return LIMB_STATUS_ERROR;

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    foreach($this->map as $key => $setter)
    {
      if ((($value = $dataspace->get($key)) !== false) && (($value = $dataspace->get($key)) !== null))
        $object->set($setter, $value);
    }

    return LIMB_STATUS_OK;
  }
}

?>
