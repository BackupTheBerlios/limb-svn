<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: RedirectCommand.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/RedirectCommand.class.php');

class RedirectFromDialogCommand
{
  function RedirectFromDialogCommand(&$service_node)
  {
    $this->service_node =& $service_node;
  }

  function perform()
  {
    if(!is_a($this->service_node, 'ServiceNode'))
      return LIMB_STATUS_ERROR;

    $toolkit =& Limb :: toolkit();
    $uow =& $toolkit->getUOW();

    $node =& $this->service_node->getNodePart();
    $path2id_translator =& $toolkit->getPath2IdTranslator();

    if($uow->isDeleted($this->service_node))
    {
      $path = $path2id_translator->getPathToNode($node->get('parent_id'));
    }
    elseif($id = $node->get('id'))
    {
      $path = $path2id_translator->getPathToNode($node->get('id'));
    }
    else
      $path = $path2id_translator->getPathToNode($node->get('parent_id'));

    $redirect_command = new RedirectCommand($path);
    return $redirect_command->perform();
  }
}


?>
