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

class RedirectToParentNodeCommand
{
  var $field_name;
  function RedirectToParentNodeCommand($field_name)
  {
    $this->field_name = $field_name;
  }

  function perform(&$context)
  {
    $toolkit =& Limb :: toolkit();

    if(!$entity =& $context->getObject($this->field_name))
      return LIMB_STATUS_ERROR;

    if(!$node =& $entity->getPart('node'))
      return LIMB_STATUS_ERROR;

    $path2id_translator =& $toolkit->getPath2IdTranslator();
    if(!$path = $path2id_translator->getPathToNode((integer)$node->get('parent_id')))
      $command =& $this->getRedirectCommand('/');
    else
      $command =& $this->getRedirectCommand($path);

    return $command->perform($context);
  }

  function & getRedirectCommand($path)
  {
    return new RedirectCommand($path);
  }
}


?>
