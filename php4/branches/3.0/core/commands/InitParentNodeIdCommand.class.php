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
  var $node;

  function InitParentNodeIdCommand(&$node)
  {
    $this->node =& $node;
  }

  function perform()
  {
    $toolkit =& Limb :: toolkit();

    $dataspace =& $toolkit->getDataspace();
    $dataspace->set('parent_node_id', $this->node->get('id'));

    return LIMB_STATUS_OK;
  }
}


?>
