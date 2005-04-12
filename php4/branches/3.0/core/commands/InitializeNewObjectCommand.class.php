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

class InitializeNewObjectCommand
{
  var $object;
  var $field_name;

  function InitializeNewObjectCommand(&$object, $field_name)
  {
    $this->object = $object;
    $this->field_name = $field_name;
  }

  function perform(&$context)
  {
    $object =& Handle :: resolve($this->object);
    $context->setObject($this->field_name, $object);

    return LIMB_STATUS_OK;
  }
}

?>
