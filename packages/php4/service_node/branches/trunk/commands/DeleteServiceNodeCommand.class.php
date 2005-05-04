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
  var $service_node;

  function DeleteServiceNodeCommand(&$service_node)
  {
    $this->service_node =& $service_node;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();

    if(!is_a($this->service_node, 'ServiceNode'))
      return LIMB_STATUS_ERROR;

    $node =& $this->service_node->getPart('node');
    $tree =& $toolkit->getTree();

    if($children =& $tree->countChildren($node->get('id')))
      return LIMB_STATUS_ERROR;

    $uow =& $toolkit->getUOW();

    $uow->delete($this->service_node);

    return LIMB_STATUS_OK;
  }
}

?>
