<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: FormProcessingCommand.class.php 1143 2005-03-05 11:04:06Z pachanga $
*
***********************************************************************************/
class InitParentNodeIdCommand
{
  function perform()
  {
    $toolkit =& Limb :: toolkit();
    if(!$mapped_object =& $toolkit->getMappedObject())
      return LIMB_STATUS_OK;

    $dataspace =& $toolkit->getDataspace();
    $dataspace->set('parent_node_id', $mapped_object->get('node_id'));

    return LIMB_STATUS_OK;
  }
}


?>
