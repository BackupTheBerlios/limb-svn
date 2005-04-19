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

class InitServiceNodeCommand
{
  var $entity_field_name;

  function InitServiceNodeCommand($entity_field_name)
  {
    $this->entity_field_name = $entity_field_name;
  }

  function perform(&$context)
  {
    $locator =& $this->getLocator();

    if(!$entity =& $locator->getCurrentServiceNode())
      return LIMB_STATUS_ERROR;

    $context->setObject($this->entity_field_name, $entity);

    return LIMB_STATUS_OK;
  }

  function & getLocator()
  {
    return new ServiceNodeLocator();
  }
}

?>
