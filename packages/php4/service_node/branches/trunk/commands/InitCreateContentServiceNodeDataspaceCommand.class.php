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
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');

class InitCreateContentServiceNodeDataspaceCommand
{
  function InitCreateContentServiceNodeDataspaceCommand(){}

  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();

    if(!$entity =& $toolkit->getCurrentEntity())
      return LIMB_STATUS_ERROR;

    if(!$node =& $entity->getPart('node'))
      return LIMB_STATUS_ERROR;

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->getDataspace();

    $dataspace->set('parent_node_id', $node->get('id'));

    return LIMB_STATUS_OK;
  }
}

?>
