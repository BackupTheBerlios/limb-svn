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

class PutCurrentEntityToContextCommand
{
  var $field_name;

  function PutCurrentEntityToContextCommand($field_name)
  {
    $this->field_name = $field_name;
  }

  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();
    if(!$entity =& $toolkit->getCurrentEntity())
      return LIMB_STATUS_ERROR;

    $context->setObject($this->field_name, $entity);

    return LIMB_STATUS_OK;
  }
}

?>
