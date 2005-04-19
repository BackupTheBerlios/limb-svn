<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: DeleteObjectCommand.class.php 1209 2005-04-08 14:29:41Z pachanga $
*
***********************************************************************************/

class DeleteServiceNodeCommand
{
  var $context_field;

  function DeleteServiceNodeCommand($context_field)
  {
    $this->context_field = $context_field;
  }

  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();

    if(!$entity =& $context->getObject($this->context_field))
      return LIMB_STATUS_ERROR;

    if(!is_a($entity, 'ServiceNode'))
      return LIMB_STATUS_ERROR;

    $node =& $entity->getPart('node');
    $tree =& $toolkit->getTree();

    if($children =& $tree->countChildren($node->get('id')))
      return LIMB_STATUS_ERROR;

    $uow =& $toolkit->getUOW();

    $uow->delete($entity);

    return LIMB_STATUS_OK;
  }
}

?>
