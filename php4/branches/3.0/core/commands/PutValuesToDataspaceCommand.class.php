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

class PutValuesToDataspaceCommand
{
  var $values;

  function PutValuesToDataspaceCommand($values)
  {
    $this->values = $values;
  }

  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();
    $dataspace->merge($this->values);

    return LIMB_STATUS_OK;
  }
}

?>
