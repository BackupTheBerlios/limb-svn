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

class RedirectToMappedNodeCommand
{
  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();
    if(!$entity =& $toolkit->getCurrentEntity() || (!is_a($entity, 'Entity')))
      return LIMB_STATUS_ERROR;

    if(!$node =& $entity->getPart('node'))
      return LIMB_STATUS_ERROR;

    $toolkit =& Limb :: toolkit();
    $path2id_translator =& $toolkit->getPath2IdTranslator();

    $path = $path2id_translator->getPathToNode($node->get('id'));
    $command =& $this->getRedirectCommand($path);
    return $command->perform($context);
  }

  function & getRedirectCommand($path)
  {
    return new RedirectCommand($path);
  }
}


?>
