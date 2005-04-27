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

class InitCreateServiceNodeDataspaceFromRRResultCommand
{
  var $resolver_name;

  function InitCreateServiceNodeDataspaceFromRRResultCommand($resolver_name)
  {
    $this->resolver_name = $resolver_name;
  }

  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();
    $resolver =& $toolkit->getRequestResolver($this->resolver_name);
    if(!is_object($resolver))
      return LIMB_STATUS_ERROR;

    if(!$entity =& $resolver->resolve($toolkit->getRequest()))
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
