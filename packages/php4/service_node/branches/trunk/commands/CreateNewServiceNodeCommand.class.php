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
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNodeLocator.class.php');

class CreateNewServiceNodeCommand
{
  var $entity_field_name;

  function CreateNewServiceNodeCommand($entity_field_name)
  {
    $this->entity_field_name = $entity_field_name;
  }

  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();
    $class =& $dataspace->get('class_name');

    $entity = $toolkit->createObject($class);
    $context->setObject($this->entity_field_name, $entity);

    return LIMB_STATUS_OK;
  }

  function & getLocator()
  {
    return new ServiceNodeLocator();
  }
}

?>
